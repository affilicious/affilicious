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

        die('[{"id":null,"name":"Stirb langsam 1-5 [Blu-ray]","slug":"stirb-langsam-1-5-blu-ray","thumbnail":{"id":null,"src":"https:\/\/images-eu.ssl-images-amazon.com\/images\/I\/61XxirYs7OL.jpg"},"excerpt":null,"content":null,"image_gallery":[null,null],"details":null,"shops":[{"template_id":49,"name":"Amazon","slug":"amazon","updated_at":"6 Juli 2017 16:07 Uhr","thumbnail_id":820,"tracking":{"affiliate_link":"https:\/\/www.amazon.de\/Stirb-langsam-Blu-ray-Bruce-Willis\/dp\/B00BU6MTAW?SubscriptionId=AKIAI2EBMSMKNKC43YCA&tag=affiliciousth-21&linkCode=xm2&camp=2025&creative=165953&creativeASIN=B00BU6MTAW","affiliate_product_id":"B00BU6MTAW","affiliate_id":"B00BU6MTAW"},"pricing":{"availability":"available","price":{"value":"22.80","currency":{"value":"EUR","label":"Euro","symbol":"\u20ac"}},"old_price":{"value":"22.80","currency":{"value":"EUR","label":"Euro","symbol":"\u20ac"}}}}],"review":null,"tags":null,"related_products":null,"related_accessories":null},{"id":null,"name":"Stirb langsam 1-5 [5 DVDs]","slug":"stirb-langsam-1-5-5-dvds","thumbnail":{"id":null,"src":"https:\/\/images-eu.ssl-images-amazon.com\/images\/I\/61Hkijbj%2BhL.jpg"},"excerpt":null,"content":null,"image_gallery":[null,null],"details":null,"shops":[{"template_id":49,"name":"Amazon","slug":"amazon","updated_at":"6 Juli 2017 16:07 Uhr","thumbnail_id":820,"tracking":{"affiliate_link":"https:\/\/www.amazon.de\/Stirb-langsam-1-5-5-DVDs\/dp\/B00BU6MTTS?SubscriptionId=AKIAI2EBMSMKNKC43YCA&tag=affiliciousth-21&linkCode=xm2&camp=2025&creative=165953&creativeASIN=B00BU6MTTS","affiliate_product_id":"B00BU6MTTS","affiliate_id":"B00BU6MTTS"},"pricing":{"availability":"available","price":{"value":"21.49","currency":{"value":"EUR","label":"Euro","symbol":"\u20ac"}},"old_price":{"value":"21.49","currency":{"value":"EUR","label":"Euro","symbol":"\u20ac"}}}}],"review":null,"tags":null,"related_products":null,"related_accessories":null},{"id":null,"name":"Stirb langsam 5 - Ein guter Tag zum Sterben - Extended Cut [Blu-ray]","slug":"stirb-langsam-5-ein-guter-tag-zum-sterben-extended-cut-blu-ray","thumbnail":{"id":null,"src":"https:\/\/images-eu.ssl-images-amazon.com\/images\/I\/61UpCEMevSL.jpg"},"excerpt":null,"content":null,"image_gallery":[null,null,null,null,null,null],"details":null,"shops":[{"template_id":49,"name":"Amazon","slug":"amazon","updated_at":"6 Juli 2017 16:07 Uhr","thumbnail_id":820,"tracking":{"affiliate_link":"https:\/\/www.amazon.de\/Stirb-langsam-Sterben-Extended-Blu-ray\/dp\/B00BEDDLC6?SubscriptionId=AKIAI2EBMSMKNKC43YCA&tag=affiliciousth-21&linkCode=xm2&camp=2025&creative=165953&creativeASIN=B00BEDDLC6","affiliate_product_id":"B00BEDDLC6","affiliate_id":"B00BEDDLC6"},"pricing":{"availability":"available","price":{"value":"7.64","currency":{"value":"EUR","label":"Euro","symbol":"\u20ac"}},"old_price":{"value":"7.64","currency":{"value":"EUR","label":"Euro","symbol":"\u20ac"}}}}],"review":null,"tags":null,"related_products":null,"related_accessories":null},{"id":null,"name":"Stirb langsam 1 [Blu-ray]","slug":"stirb-langsam-1-blu-ray","thumbnail":{"id":null,"src":"https:\/\/images-eu.ssl-images-amazon.com\/images\/I\/51Fkb1oaHML.jpg"},"excerpt":null,"content":null,"image_gallery":[null,null],"details":null,"shops":[{"template_id":49,"name":"Amazon","slug":"amazon","updated_at":"6 Juli 2017 16:07 Uhr","thumbnail_id":820,"tracking":{"affiliate_link":"https:\/\/www.amazon.de\/Stirb-langsam-Blu-ray-Bruce-Willis\/dp\/B00AEJO20Q?SubscriptionId=AKIAI2EBMSMKNKC43YCA&tag=affiliciousth-21&linkCode=xm2&camp=2025&creative=165953&creativeASIN=B00AEJO20Q","affiliate_product_id":"B00AEJO20Q","affiliate_id":"B00AEJO20Q"},"pricing":{"availability":"available","price":{"value":"9.79","currency":{"value":"EUR","label":"Euro","symbol":"\u20ac"}},"old_price":{"value":"9.79","currency":{"value":"EUR","label":"Euro","symbol":"\u20ac"}}}}],"review":null,"tags":null,"related_products":null,"related_accessories":null},{"id":null,"name":"Stirb langsam","slug":"stirb-langsam","thumbnail":{"id":null,"src":"https:\/\/images-eu.ssl-images-amazon.com\/images\/I\/511VtZLUKwL.jpg"},"excerpt":null,"content":null,"image_gallery":[null,null],"details":null,"shops":[{"template_id":49,"name":"Amazon","slug":"amazon","updated_at":"6 Juli 2017 16:07 Uhr","thumbnail_id":820,"tracking":{"affiliate_link":"https:\/\/www.amazon.de\/Stirb-langsam-Bruce-Willis\/dp\/B000287VCU?SubscriptionId=AKIAI2EBMSMKNKC43YCA&tag=affiliciousth-21&linkCode=xm2&camp=2025&creative=165953&creativeASIN=B000287VCU","affiliate_product_id":"B000287VCU","affiliate_id":"B000287VCU"},"pricing":{"availability":"available","price":{"value":"5.73","currency":{"value":"EUR","label":"Euro","symbol":"\u20ac"}},"old_price":{"value":"5.73","currency":{"value":"EUR","label":"Euro","symbol":"\u20ac"}}}}],"review":null,"tags":null,"related_products":null,"related_accessories":null},{"id":null,"name":"Stirb langsam 2","slug":"stirb-langsam-2","thumbnail":{"id":null,"src":"https:\/\/images-eu.ssl-images-amazon.com\/images\/I\/612vyFXH4AL.jpg"},"excerpt":null,"content":null,"image_gallery":[null,null],"details":null,"shops":[{"template_id":49,"name":"Amazon","slug":"amazon","updated_at":"6 Juli 2017 16:07 Uhr","thumbnail_id":820,"tracking":{"affiliate_link":"https:\/\/www.amazon.de\/Stirb-langsam-2-Bruce-Willis\/dp\/B00004RYUF?SubscriptionId=AKIAI2EBMSMKNKC43YCA&tag=affiliciousth-21&linkCode=xm2&camp=2025&creative=165953&creativeASIN=B00004RYUF","affiliate_product_id":"B00004RYUF","affiliate_id":"B00004RYUF"},"pricing":{"availability":"available","price":{"value":"5.86","currency":{"value":"EUR","label":"Euro","symbol":"\u20ac"}},"old_price":{"value":"5.86","currency":{"value":"EUR","label":"Euro","symbol":"\u20ac"}}}}],"review":null,"tags":null,"related_products":null,"related_accessories":null},{"id":null,"name":"Stirb langsam 2 [Blu-ray]","slug":"stirb-langsam-2-blu-ray","thumbnail":{"id":null,"src":"https:\/\/images-eu.ssl-images-amazon.com\/images\/I\/511lKcoqcZL.jpg"},"excerpt":null,"content":null,"image_gallery":[null,null],"details":null,"shops":[{"template_id":49,"name":"Amazon","slug":"amazon","updated_at":"6 Juli 2017 16:07 Uhr","thumbnail_id":820,"tracking":{"affiliate_link":"https:\/\/www.amazon.de\/Stirb-langsam-Blu-ray-Bruce-Willis\/dp\/B00AEJO28I?SubscriptionId=AKIAI2EBMSMKNKC43YCA&tag=affiliciousth-21&linkCode=xm2&camp=2025&creative=165953&creativeASIN=B00AEJO28I","affiliate_product_id":"B00AEJO28I","affiliate_id":"B00AEJO28I"},"pricing":{"availability":"available","price":{"value":"8.55","currency":{"value":"EUR","label":"Euro","symbol":"\u20ac"}},"old_price":{"value":"8.55","currency":{"value":"EUR","label":"Euro","symbol":"\u20ac"}}}}],"review":null,"tags":null,"related_products":null,"related_accessories":null},{"id":null,"name":"Stirb langsam - Jetzt erst recht [Blu-ray]","slug":"stirb-langsam-jetzt-erst-recht-blu-ray","thumbnail":{"id":null,"src":"https:\/\/images-eu.ssl-images-amazon.com\/images\/I\/51AZRLM87ZL.jpg"},"excerpt":null,"content":null,"image_gallery":[null,null,null],"details":null,"shops":[{"template_id":49,"name":"Amazon","slug":"amazon","updated_at":"6 Juli 2017 16:07 Uhr","thumbnail_id":820,"tracking":{"affiliate_link":"https:\/\/www.amazon.de\/Stirb-langsam-Jetzt-recht-Blu-ray\/dp\/B001EKNKAW?SubscriptionId=AKIAI2EBMSMKNKC43YCA&tag=affiliciousth-21&linkCode=xm2&camp=2025&creative=165953&creativeASIN=B001EKNKAW","affiliate_product_id":"B001EKNKAW","affiliate_id":"B001EKNKAW"},"pricing":{"availability":"available","price":{"value":"7.99","currency":{"value":"EUR","label":"Euro","symbol":"\u20ac"}},"old_price":{"value":"7.99","currency":{"value":"EUR","label":"Euro","symbol":"\u20ac"}}}}],"review":null,"tags":null,"related_products":null,"related_accessories":null},{"id":null,"name":"Stirb langsam 4.0 [Blu-ray]","slug":"stirb-langsam-4-0-blu-ray","thumbnail":{"id":null,"src":"https:\/\/images-eu.ssl-images-amazon.com\/images\/I\/513mr0CPXeL.jpg"},"excerpt":null,"content":null,"image_gallery":[null,null],"details":null,"shops":[{"template_id":49,"name":"Amazon","slug":"amazon","updated_at":"6 Juli 2017 16:07 Uhr","thumbnail_id":820,"tracking":{"affiliate_link":"https:\/\/www.amazon.de\/Stirb-langsam-Blu-ray-Bruce-Willis\/dp\/B00AEJO2GU?SubscriptionId=AKIAI2EBMSMKNKC43YCA&tag=affiliciousth-21&linkCode=xm2&camp=2025&creative=165953&creativeASIN=B00AEJO2GU","affiliate_product_id":"B00AEJO2GU","affiliate_id":"B00AEJO2GU"},"pricing":{"availability":"available","price":{"value":"9.99","currency":{"value":"EUR","label":"Euro","symbol":"\u20ac"}},"old_price":{"value":"9.99","currency":{"value":"EUR","label":"Euro","symbol":"\u20ac"}}}}],"review":null,"tags":null,"related_products":null,"related_accessories":null},{"id":null,"name":"Stirb langsam 4.0","slug":"stirb-langsam-4-0","thumbnail":{"id":null,"src":"https:\/\/images-eu.ssl-images-amazon.com\/images\/I\/61T89mAoIhL.jpg"},"excerpt":null,"content":null,"image_gallery":[null,null,null,null,null,null,null,null],"details":null,"shops":[{"template_id":49,"name":"Amazon","slug":"amazon","updated_at":"6 Juli 2017 16:07 Uhr","thumbnail_id":820,"tracking":{"affiliate_link":"https:\/\/www.amazon.de\/Stirb-langsam-4-0-Bruce-Willis\/dp\/B000UYQ5AU?SubscriptionId=AKIAI2EBMSMKNKC43YCA&tag=affiliciousth-21&linkCode=xm2&camp=2025&creative=165953&creativeASIN=B000UYQ5AU","affiliate_product_id":"B000UYQ5AU","affiliate_id":"B000UYQ5AU"},"pricing":{"availability":"available","price":{"value":"5.51","currency":{"value":"EUR","label":"Euro","symbol":"\u20ac"}},"old_price":{"value":"5.51","currency":{"value":"EUR","label":"Euro","symbol":"\u20ac"}}}}],"review":null,"tags":null,"related_products":null,"related_accessories":null}]');

        $amazon_provider = $this->find_amazon_provider();

        $response = $this->_search("Gardena", $amazon_provider);

        $results = $response['Items']['Item'];
        $products = array_map(function(array $result) {
            return $this->create_product($result, [
                'store_thumbnail' => false,
                'store_image_gallery' => false,
                'store_shop' => false,
                'store_attributes' => false,
            ]);
        }, $results);


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
     * @param Amazon_Provider $amazon_provider
     * @return array|\WP_Error
     */
    private function _search($keywords, Amazon_Provider $amazon_provider)
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

        $response_group = ['Large'];

        $search = new Search();
        $search->setCategory('DVD');
        $search->setActor('Bruce Willis');
        $search->setKeywords('Die Hard');
        //$search->setKeywords($keywords);
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

        if($config['variants'] === true && isset($item['Variations'])) {
            $product = new Complex_Product($name, $slug);
        } elseif ($config['variants'] === true && $parent !== null && isset($item['VariationAttributes'])) {
            $product = new Product_Variant($parent, $name, $slug);
        } else {
            $product = new Simple_Product($name, $slug);
        }

        if($product instanceof Complex_Product) {
            $variant_items = $item['Variations']['Item'];
            foreach ($variant_items as $variant_item) {
                // The variants doesn't have a affiliate link
                $variant_item = wp_parse_args($variant_item, [
                    'DetailPageURL' => $item['DetailPageURL']
                ]);

                /** @var Product_Variant $product_variant */
                $product_variant = $this->create_product($variant_item, $config, $product);
                $product->add_variant($product_variant);
            }

            $default_variant = $product->get_default_variant();
            if($default_variant !== null) {
                $variant_thumbnail_id = $default_variant->get_thumbnail_id();
                $product->set_thumbnail_id($variant_thumbnail_id);
            }
        }

        if($product instanceof Product_Variant) {
            $attributes = Amazon_Helper::find_attributes($item, !empty($config['store_attributes']));
            foreach ($attributes as $attribute) {
                $product->add_attribute($attribute);
            }
        }

        if($product instanceof Shop_Aware_Interface) {
            $shop = Amazon_Helper::find_shop($item, null, !empty($config['store_shop']));
            if ($shop !== null) {
                $product->add_shop($shop);
            }
        }

        $thumbnail_id = Amazon_Helper::find_thumbnail_id($item, !empty($config['store_thumbnail']));
        if($thumbnail_id !== null) {
            $product->set_thumbnail_id($thumbnail_id);
        }

        $image_gallery_ids = Amazon_Helper::find_image_gallery_ids($item, !empty($config['store_image_gallery']));
        if(!empty($image_gallery_ids)) {
            $product->set_image_gallery($image_gallery_ids);
        }

        $product = apply_filters('aff_amazon_import_create_product', $product, $item, $config);

        return $product;
    }
}
