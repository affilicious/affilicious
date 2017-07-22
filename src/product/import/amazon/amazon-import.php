<?php
namespace Affilicious\Product\Import\Amazon;

use Affilicious\Common\Model\Slug;
use Affilicious\Product\Helper\Amazon_Helper;
use Affilicious\Product\Import\Import_Interface;
use Affilicious\Provider\Model\Amazon\Amazon_Provider;
use Affilicious\Provider\Repository\Provider_Repository_Interface;
use Affilicious\Shop\Model\Affiliate_Product_Id;
use ApaiIO\ApaiIO;
use ApaiIO\Configuration\GenericConfiguration;
use ApaiIO\Operations\Lookup;
use ApaiIO\Request\GuzzleRequest;
use ApaiIO\ResponseTransformer\XmlToArray;
use GuzzleHttp\Client;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Amazon_Import implements Import_Interface
{
    /**
     * @var Provider_Repository_Interface
     */
    private $provider_repository;

    /**
     * @since 0.9
     * @param Provider_Repository_Interface $provider_repository
     */
    public function __construct(Provider_Repository_Interface $provider_repository)
    {
        $this->provider_repository = $provider_repository;
    }

    /**
     * @inheritdoc
     * @since 0.9
     */
    public function import(Affiliate_Product_Id $affiliate_product_id, array $config = [])
    {
        $config = wp_parse_args($config, [
            'variants' => false,
            'store_attributes' => true,
            'store_shop' => true,
            'store_thumbnail' => true,
            'store_image_gallery' => true
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
        $product = Amazon_Helper::create_product($item, $config);
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
}
