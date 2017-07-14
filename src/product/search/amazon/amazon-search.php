<?php
namespace Affilicious\Product\Search\Amazon;

use Affilicious\Common\Generator\Slug_Generator_Interface;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Product\Helper\Amazon_Helper;
use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Product_Variant;
use Affilicious\Product\Model\Shop_Aware_Interface;
use Affilicious\Product\Model\Simple_Product;
use Affilicious\Product\Search\Search_Interface;
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

class Amazon_Search implements Search_Interface
{
    const CATEGORY_ALL = 'All';
    const CATEGORY_BOOKS = 'Books';
    const CATEGORY_DVD = 'DVD';
    const CATEGORY_MUSIC = 'Music';
    const CATEGORY_APPAREL = 'Apparel';
    const CATEGORY_VIDEO = 'Video';
    const CATEGORY_JEWELRY = 'Jewelry';
    const CATEGORY_AUTOMOTIVE = 'Automotive';
    const CATEGORY_WATCH = 'Watch';
    const CATEGORY_ELECTRONICS = 'Electronics';

    public static $categories = [
        self::CATEGORY_ALL,
        self::CATEGORY_BOOKS,
        self::CATEGORY_DVD,
        self::CATEGORY_MUSIC,
        self::CATEGORY_APPAREL,
        self::CATEGORY_VIDEO,
        self::CATEGORY_JEWELRY,
        self::CATEGORY_AUTOMOTIVE,
        self::CATEGORY_WATCH,
        self::CATEGORY_ELECTRONICS,
    ];

    /**
     * @var Provider_Repository_Interface
     */
    protected $provider_repository;

    /**
     * @var Slug_Generator_Interface
     */
    protected $slug_generator;

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
     * @inheritdoc
     * @since 0.9
     */
    public function search(array $params)
    {
        $term = $this->find_term($params);
        if($term instanceof \WP_Error) {
            return $term;
        }

        $type = $this->find_type($params);
        if($type instanceof \WP_Error) {
            return $type;
        }

        $category = $this->find_category($params);
        if($category instanceof \WP_Error) {
            return $category;
        }

        $provider = $this->find_provider();
        if($provider instanceof \WP_Error) {
            return $provider;
        }

        $response = $this->request($term, $type, $category, $provider);
        if($response instanceof \WP_Error) {
            return $response;
        }

        $results = $response['Items']['Item'];
        $products = array_map(function (array $result) {
            return $this->create_product($result, [
                'variants' => true,
                'store_thumbnail' => false,
                'store_image_gallery' => false,
                'store_shop' => false,
                'store_attributes' => false,
            ]);

        }, $results);

        return $products;
    }

    /**
     * Find the search term in the parameters.
     *
     * @since 0.9
     * @param array $params The parameters for the Amazon search.
     * @return string|\WP_Error Either the term or an error.
     */
    protected function find_term(array $params)
    {
        $term = isset($params['term']) ? $params['term'] : null;
        if(empty($term)) {
            return new \WP_Error('aff_amazon_search_missing_term', 'The Amazon search term with the key "term" is missing in the parameters.');
        }

        return $term;
    }

    /**
     * Find the search type in the parameters.
     *
     * @since 0.9
     * @param array $params The parameters for the Amazon search.
     * @return string|\WP_Error Either the type or an error.
     */
    protected function find_type(array $params)
    {
        $type = isset($params['type']) ? $params['type'] : null;
        if(empty($type)) {
            return new \WP_Error('aff_amazon_search_missing_type', 'The Amazon search type with the key "type" is missing in the parameter.');
        }

        return $type;
    }

    /**
     * Find the search category in the parameters.
     *
     * @since 0.9
     * @param array $params The parameters for the Amazon search.
     * @return string|\WP_Error Either the category or an error.
     */
    protected function find_category(array $params)
    {
        $category = isset($params['category']) ? $params['category'] : null;
        if(empty($category)) {
            return new \WP_Error('aff_amazon_search_missing_category', 'The Amazon search category with the key "category" is missing in the parameters.');
        }

        if(!in_array($category, self::$categories)) {
            return new \WP_Error('aff_amazon_search_invalid_category', sprintf(
                'The Amazon search category is not valid. Choose from: %s',
                explode(', ', self::$categories)
            ));
        }

        return $category;
    }

    /**
     * Find the amazon provider for the search.
     *
     * @since 0.9
     * @return Amazon_Provider|\WP_Error Either the provider.
     */
    protected function find_provider()
    {
        $amazon_provider = $this->provider_repository->find_one_by_slug(new Slug('amazon'));
        if(!($amazon_provider instanceof Amazon_Provider)) {
            return new \WP_Error('aff_amazon_search_missing_provider', "The Amazon provider with the slug 'amazon' hasn't been found.");
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
    protected function request($keywords, $type, $category, Amazon_Provider $amazon_provider)
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
    protected function create_product(array $item, array $config, Complex_Product $parent = null)
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

        //$product = apply_filters('aff_amazon_import_create_product', $product, $item, $config);

        return $product;
    }
}
