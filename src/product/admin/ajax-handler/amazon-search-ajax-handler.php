<?php
namespace Affilicious\Product\Admin\Ajax_Handler;

use Affilicious\Common\Generator\Slug_Generator_Interface;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Product\Helper\Amazon_Helper;
use Affilicious\Product\Helper\Product_Helper;
use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Product_Variant;
use Affilicious\Product\Model\Shop_Aware_Interface;
use Affilicious\Product\Model\Simple_Product;
use Affilicious\Provider\Model\Amazon\Amazon_Provider;
use Affilicious\Provider\Repository\Provider_Repository_Interface;
use ApaiIO\ApaiIO;
use ApaiIO\Configuration\GenericConfiguration;
use ApaiIO\Operations\Search;
use ApaiIO\Request\GuzzleRequest;
use ApaiIO\ResponseTransformer\XmlToArray;
use GuzzleHttp\Client;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Amazon_Search_Ajax_Handler
{
    /**
     * @var Provider_Repository_Interface
     */
    private $provider_repository;

    /**
     * @var Slug_Generator_Interface
     */
    private $slug_generator;

    /**
     * @since 0.9
     * @param Provider_Repository_Interface $provider_repository
     * @param Slug_Generator_Interface $slug_generator
     */
    public function __construct(Provider_Repository_Interface $provider_repository, Slug_Generator_Interface $slug_generator)
    {
        $this->provider_repository = $provider_repository;
        $this->slug_generator = $slug_generator;
    }

    /**
     * @hook wp_ajax_aff_product_admin_amazon_search
     * @since 0.9
     */
    function search()
    {
        $term = isset($_GET['term']) ? $_GET['term'] : null;
        $type = isset($_GET['type']) ? $_GET['type'] : null;
        $category = isset($_GET['category']) ? $_GET['category'] : null;

        if(empty($term)) {
            status_header(400);
            wp_die('The Amazon search term is missing.');
        }

        if(empty($type)) {
            status_header(400);
            wp_die('The Amazon search type is missing.');
        }

        if(empty($category)) {
            status_header(400);
            wp_die('The Amazon search category is missing.');
        }

        $amazon_provider = $this->find_amazon_provider();

        $category = $this->map_category($category);
        $response = $this->_search($term, $type, $category, $amazon_provider);

        $results = $response['Items']['Item'];

        try {
            $products = array_map(function (array $result) {
                $r = $this->create_product($result, [
                    'variants' => true,
                    'store_thumbnail' => false,
                    'store_image_gallery' => false,
                    'store_shop' => false,
                    'store_attributes' => false,
                ]);

                return $r;
            }, $results);
        } catch (\Exception $e) {
            $i = $e;
        }

        $r = [];
        foreach ($products as $product) {
            $r[] = Product_Helper::to_array($product);
        }

        $result = json_encode($r);

        die($result);
    }

    /**
     * Find the required Amazon provider which holds all credentials.
     *
     * @since 0.9
     * @return Amazon_Provider|\WP_Error
     */
    private function find_amazon_provider()
    {
        $amazon_provider = $this->provider_repository->find_one_by_slug(new Slug('amazon'));
        if($amazon_provider === null) {
            $amazon_provider = new \WP_Error('aff_failed_to_find_amazon_provider', 'The Amazon provider with the slug "amazon" haven\'t been found.');
        }

        return $amazon_provider;
    }

    /**
     * Lookup the Amazon product by the product ID.
     *
     * @since 0.9
     * @param string $keywords
     * @param $type
     * @param $category
     * @param Amazon_Provider $amazon_provider
     * @return array|\WP_Error
     */
    private function _search($keywords, $type, $category, Amazon_Provider $amazon_provider)
    {
        $conf = new GenericConfiguration();
        $client = new Client();
        $request = new GuzzleRequest($client);

        $conf
            ->setCountry($amazon_provider->get_country()->get_value())
            ->setAccessKey($amazon_provider->get_access_key()->get_value())
            ->setSecretKey($amazon_provider->get_secret_key()->get_value())
            ->setAssociateTag($amazon_provider->get_associate_tag()->get_value())
            ->setRequest($request)
            ->setResponseTransformer(new XmlToArray());

        $apaiIO = new ApaiIO($conf);

        $response_group = ['Small', 'Images', 'Offers', 'ItemAttributes', 'VariationMatrix', 'VariationOffers'];

        $search = new Search();

        $search->setCategory($category);
        $search->setKeywords($keywords);
        $search->setResponsegroup($response_group);

        try {
            $response = $apaiIO->runOperation($search);
        } catch (\Exception $e) {
            $response = new \WP_Error('aff_failed_to_search_amazon_products', $e->getMessage());
        }

        if(isset($response['Items']['Request']['Errors']['Error'])) {
            $errors = $response['Items']['Request']['Errors']['Error'];
            $response = new \WP_Error();
            foreach ($errors as $error) {
                $response->add('aff_failed_to_search_amazon_products', $error['Message']);
            }
        }

        return $response;
    }

    /**
     * Create the product from the response item.
     *
     * @since 0.9
     * @param array $item
     * @param array $config
     * @param Complex_Product|null $parent
     * @return Product|\WP_Error
     */
    private function create_product(array $item, array $config, Complex_Product $parent = null)
    {
        $name = new Name($item['ItemAttributes']['Title']);
        $slug = $this->slug_generator->generate_from_name($name);

        if ($config['variants'] === true && isset($item['Variations'])) {
            $product = new Complex_Product($name, $slug);
        } elseif ($config['variants'] === true && $parent !== null && isset($item['VariationAttributes'])) {
            $product = new Product_Variant($parent, $name, $slug);
        } else {
            $product = new Simple_Product($name, $slug);
        }

        if ($product instanceof Complex_Product) {
            $variant_items = $item['Variations']['Item'];
            foreach ($variant_items as $variant_item) {
                // The variants doesn't have a affiliate link
                $variant_item = wp_parse_args($variant_item, [
                    'DetailPageURL' => $item['DetailPageURL']
                ]);

                /** @var Product_Variant $product_variant */
                $product_variant = $this->create_product($variant_item, $config, $product);
                if($product_variant !== null) {
                    $product->add_variant($product_variant);
                }
            }

            $default_variant = $product->get_default_variant();
            if ($default_variant !== null) {
                $variant_thumbnail_id = $default_variant->get_thumbnail_id();
                $product->set_thumbnail_id($variant_thumbnail_id);
            }
        }

        if ($product instanceof Product_Variant) {
            $attributes = Amazon_Helper::find_attributes($item, !empty($config['store_attributes']));
            foreach ($attributes as $attribute) {
                $product->add_attribute($attribute);
            }
        }

        if ($product instanceof Shop_Aware_Interface) {
            $shop = Amazon_Helper::find_shop($item, null, !empty($config['store_shop']));
            if ($shop !== null) {
                $product->add_shop($shop);
            }
        }

        $thumbnail_id = Amazon_Helper::find_thumbnail_id($item, !empty($config['store_thumbnail']));
        if ($thumbnail_id !== null) {
            $product->set_thumbnail_id($thumbnail_id);
        }

        $image_gallery_ids = Amazon_Helper::find_image_gallery_ids($item, !empty($config['store_image_gallery']));
        if (!empty($image_gallery_ids)) {
            $product->set_image_gallery($image_gallery_ids);
        }

        $product = apply_filters('aff_amazon_import_create_product', $product, $item, $config);

        return $product;
    }

    /**
     * Map the category value into a valid search category.
     *
     * @since 0.9
     * @param string $value
     * @return string|\WP_Error
     */
    private function map_category($value)
    {
        $valid = [
            'all' => 'All',
            'books' => 'Books',
            'dvd' => 'DVD',
            'music' => 'Music',
            'apparel' => 'Apparel',
            'video' => 'Video',
            'jewelry' => 'Jewelry',
            'automotive' => 'Automotive',
            'watch' => 'Watch',
            'electronics' => 'Electronics'
        ];

        if(!isset($valid[$value])) {
            return new \WP_Error('aff_amazon_product_search_category', sprintf(
                'The category "%s" is invalid'
            ));
        }

        $category = $valid[$value];

        return $category;
    }
}
