<?php
namespace Affilicious\Product\Importer;

use Affilicious\Common\Generator\Slug_Generator_Interface;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Product\Helper\Amazon_Helper;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Simple_Product;
use Affilicious\Provider\Model\Amazon\Amazon_Provider;
use Affilicious\Provider\Repository\Provider_Repository_Interface;
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
     * @var Slug_Generator_Interface
     */
    private $slug_generator;

    /**
     * @since 0.9
     * @param Provider_Repository_Interface $provider_repository
     * @param Shop_Template_Repository_Interface $shop_template_repository
     * @param Slug_Generator_Interface $slug_generator
     */
    public function __construct(
        Provider_Repository_Interface $provider_repository,
        Shop_Template_Repository_Interface $shop_template_repository,
        Slug_Generator_Interface $slug_generator
    ) {
        $this->provider_repository = $provider_repository;
        $this->shop_template_repository = $shop_template_repository;
        $this->slug_generator = $slug_generator;
    }

    /**
     * @inheritdoc
     * @since 0.9
     */
    public function import(Affiliate_Product_Id $affiliate_product_id, array $config = [])
    {
        $amazon_provider = $this->find_amazon_provider();
        if($amazon_provider instanceof \WP_Error) {
            return $amazon_provider;
        }

        $response = $this->lookup($affiliate_product_id, $amazon_provider);
        if($response instanceof \WP_Error) {
            return $response;
        }

        $product = $this->create_product($response, $config);
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
     * @return array|\WP_Error
     */
    private function lookup(Affiliate_Product_Id $affiliate_product_id, Amazon_Provider $amazon_provider)
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

        $response_group = ['Large', 'Variations'];
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
     * Create the product from the response.
     *
     * @since 0.9
     * @param array $response
     * @param array $config
     * @return Product|\WP_Error
     */
    private function create_product(array $response, array $config)
    {
        $item = $response['Items']['Item'];
        $name = new Name($item['ItemAttributes']['Title']);
        $slug = $this->slug_generator->generate_from_name($name);

        $product = new Simple_Product($name, $slug);

        $shop = $this->create_shop($item, new Shop_Template_Id(49));
        if(!($shop instanceof \WP_Error)) {
            $shop = apply_filters('aff_amazon_import_create_shop', $shop, $product, $item);
            $product->add_shop($shop);
        }

        $product = apply_filters('aff_amazon_import_create_product', $product, $item, $config);

        return $product;
    }

    /**
     * Create the shop from the item and shop template.
     *
     * @since 0.9
     * @param array $item
     * @param Shop_Template_Id $shop_template_id
     * @return Shop|\WP_Error
     */
    private function create_shop(array $item, Shop_Template_Id $shop_template_id)
    {
        $shop_template = $this->shop_template_repository->find_one_by_id($shop_template_id);
        if($shop_template === null) {
            return new \WP_Error('aff_shop_template_not_found', sprintf(
                'The shop template with the id #%d was not found.',
                $shop_template_id->get_value()
            ));
        }

        $tracking = Amazon_Helper::find_tracking($item);
        if($tracking === null) {
            return new \WP_Error('aff_shop_not_created', sprintf(
                'The tracking data containing the affiliate link and affiliate product ID for the shop "%s" was not found.',
                $shop_template->get_name()->get_value()
            ));
        }

        $pricing = Amazon_Helper::find_pricing($item);
        if($pricing === null) {
            return new \WP_Error('aff_shop_not_created', sprintf(
                'The pricing data containing the availability, price and old price for the shop "%s" was not found.',
                $shop_template->get_name()->get_value()
            ));
        }

        $shop = $shop_template->build($tracking, $pricing);

        return $shop;
    }
}
