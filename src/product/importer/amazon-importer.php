<?php
namespace Affilicious\Product\Importer;

use Affilicious\Attribute\Factory\Attribute_Template_Factory_Interface;
use Affilicious\Attribute\Model\Attribute;
use Affilicious\Attribute\Repository\Attribute_Template_Repository_Interface;
use Affilicious\Common\Generator\Slug_Generator_Interface;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Detail\Factory\Detail_Template_Factory_Interface;
use Affilicious\Detail\Model\Detail;
use Affilicious\Detail\Repository\Detail_Template_Repository_Interface;
use Affilicious\Product\Helper\Amazon_Helper;
use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Product_Variant;
use Affilicious\Product\Model\Shop_Aware_Interface;
use Affilicious\Product\Model\Simple_Product;
use Affilicious\Provider\Model\Amazon\Amazon_Provider;
use Affilicious\Provider\Repository\Provider_Repository_Interface;
use Affilicious\Shop\Factory\Shop_Template_Factory_Interface;
use Affilicious\Shop\Model\Affiliate_Product_Id;
use Affilicious\Shop\Model\Shop;
use Affilicious\Shop\Model\Shop_Template_Id;
use Affilicious\Shop\Repository\Shop_Template_Repository_Interface;
use ApaiIO\ApaiIO;
use ApaiIO\Configuration\GenericConfiguration;
use ApaiIO\Operations\Lookup;
use ApaiIO\Request\GuzzleRequest;
use ApaiIO\ResponseTransformer\XmlToArray;
use GuzzleHttp\Client;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Amazon_Importer implements Importer_Interface
{
    /**
     * @var Provider_Repository_Interface
     */
    private $provider_repository;

    /**
     * @var Shop_Template_Repository_Interface
     */
    private $shop_template_repository;

    /**
     * @var Shop_Template_Factory_Interface
     */
    private $shop_template_factory;

    /**
     * @var Attribute_Template_Repository_Interface
     */
    private $attribute_template_repository;

    /**
     * @var Attribute_Template_Factory_Interface
     */
    private $attribute_template_factory;

    /**
     * @var Detail_Template_Repository_Interface
     */
    private $detail_template_repository;

    /**
     * @var Detail_Template_Factory_Interface
     */
    private $detail_template_factory;

    /**
     * @var Slug_Generator_Interface
     */
    private $slug_generator;

    /**
     * @since 0.9
     * @param Provider_Repository_Interface $provider_repository
     * @param Shop_Template_Repository_Interface $shop_template_repository
     * @param Shop_Template_Factory_Interface $shop_template_factory
     * @param Attribute_Template_Repository_Interface $attribute_template_repository
     * @param Attribute_Template_Factory_Interface $attribute_template_factory
     * @param Detail_Template_Repository_Interface $detail_template_repository
     * @param Detail_Template_Factory_Interface $detail_template_Factory
     * @param Slug_Generator_Interface $slug_generator
     */
    public function __construct(
        Provider_Repository_Interface $provider_repository,
        Shop_Template_Repository_Interface $shop_template_repository,
        Shop_Template_Factory_Interface $shop_template_factory,
        Attribute_Template_Repository_Interface $attribute_template_repository,
        Attribute_Template_Factory_Interface $attribute_template_factory,
        Detail_Template_Repository_Interface $detail_template_repository,
        Detail_Template_Factory_Interface $detail_template_Factory,
        Slug_Generator_Interface $slug_generator
    ) {
        $this->provider_repository = $provider_repository;
        $this->shop_template_repository = $shop_template_repository;
        $this->shop_template_factory = $shop_template_factory;
        $this->attribute_template_repository = $attribute_template_repository;
        $this->attribute_template_factory = $attribute_template_factory;
        $this->detail_template_repository = $detail_template_repository;
        $this->detail_template_factory = $detail_template_Factory;
        $this->slug_generator = $slug_generator;
    }

    /**
     * @inheritdoc
     * @since 0.9
     */
    public function import(Affiliate_Product_Id $affiliate_product_id, array $config = [])
    {
        $config = wp_parse_args($config, [
            'variants' => false
        ]);

        $amazon_provider = $this->find_amazon_provider();
        if($amazon_provider instanceof \WP_Error) {
            return $amazon_provider;
        }

        $response = $this->lookup($affiliate_product_id, $amazon_provider, $config);
        if($response instanceof \WP_Error) {
            return $response;
        }

        $item = $response['Items']['Item'];
        $product = $this->create_product($item, $config);
        if($product instanceof \WP_Error) {
            return $product;
        }

        $product = apply_filters('aff_imported_amazon_product', $product);

        return $product;
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
     * @param Affiliate_Product_Id $affiliate_product_id
     * @param Amazon_Provider $amazon_provider
     * @param array $config
     * @return array|\WP_Error
     */
    private function lookup(Affiliate_Product_Id $affiliate_product_id, Amazon_Provider $amazon_provider, array $config)
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
        if($config['variants'] === true) {
            $response_group[] = 'Variations';
        }

        $response_group = apply_filters('aff_amazon_import_lookup_response_group', $response_group, $affiliate_product_id);

        $lookup = new Lookup();
        $lookup->setItemId($affiliate_product_id->get_value());
        $lookup->setResponseGroup($response_group);

        try {
            $response = $apaiIO->runOperation($lookup);
        } catch (\Exception $e) {
            $response = new \WP_Error('aff_failed_to_lookup_amazon_product', $e->getMessage());
        }

        if(isset($response['Items']['Request']['Errors']['Error'])) {
            $errors = $response['Items']['Request']['Errors']['Error'];
            $response = new \WP_Error();
            foreach ($errors as $error) {
                $response->add('aff_failed_to_lookup_amazon_product', $error['Message']);
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
        }

        if($product instanceof Product_Variant) {
            $attributes = Amazon_Helper::find_attributes($item);
            foreach ($attributes as $attribute) {
                $product->add_attribute($attribute);
            }
        }

        if($product instanceof Shop_Aware_Interface) {
            $shop = Amazon_Helper::find_shop($item);
            if ($shop !== null) {
                $product->add_shop($shop);
            }
        }

        $product = apply_filters('aff_amazon_import_create_product', $product, $item, $config);

        return $product;
    }
}
