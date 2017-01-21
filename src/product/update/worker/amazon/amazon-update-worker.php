<?php
namespace Affilicious\Product\Update\Worker\Amazon;

use Affilicious\Product\Update\Configuration\Configuration;
use Affilicious\Product\Update\Task\Batch_Update_Task_Interface;
use Affilicious\Product\Update\Worker\Abstract_Update_Worker;
use Affilicious\Product\Model\Complex\Complex_Product_Interface;
use Affilicious\Product\Model\Product_Interface;
use Affilicious\Product\Model\Shop_Aware_Product_Interface;
use Affilicious\Shop\Options\Amazon_Options;
use Affilicious\Shop\Model\Affiliate_Id;
use Affilicious\Shop\Model\Availability;
use Affilicious\Shop\Model\Currency;
use Affilicious\Shop\Model\Money;
use Affilicious\Provider\Model\Amazon\Amazon_Provider;
use ApaiIO\ApaiIO;
use ApaiIO\Configuration\GenericConfiguration;
use ApaiIO\Operations\Lookup;
use ApaiIO\Request\GuzzleRequest;
use GuzzleHttp\Client;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Amazon_Update_Worker extends Abstract_Update_Worker
{
    const PROVIDER = 'amazon';
    const UPDATE_INTERVAL = 'hourly';
    const MIN_UPDATES = 1;
    const MAX_UPDATES = 10;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function configure()
    {
        $config = new Configuration(array(
            'provider' => self::PROVIDER,
            'update_interval' => self::UPDATE_INTERVAL,
            'min_tasks' => self::MIN_UPDATES,
            'max_tasks' => self::MAX_UPDATES,
        ));

        return $config;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function execute(Batch_Update_Task_Interface $batch_update_task, $update_interval)
    {
        $provider = $batch_update_task->get_provider();
        if(!($provider instanceof Amazon_Provider)) {
            return;
        }

        $products = $batch_update_task->get_products();
        if(empty($products)) {
            return;
        }

        $affiliate_ids = $this->extract_affiliate_ids($products, self::MAX_UPDATES);
        if(empty($affiliate_ids)) {
            return;
        }

        $response = $this->item_lookups($provider, $affiliate_ids);
        if(empty($response)) {
            return;
        }

        $results = $this->map_response_to_results($response);
        if(empty($results)) {
            return;
        }

        $this->apply_results_to_products($results, $products, $update_interval);
    }

    /**
     * Extract the given number of affiliate IDs from the products.
     *
     * @since 0.7
     * @param Product_Interface[] $products
     * @param int $limit
     * @return Affiliate_Id[]
     */
    protected function extract_affiliate_ids($products, $limit)
    {
        $current = 0;

        $affiliate_ids = array();
        foreach ($products as $product) {

            if($product instanceof Complex_Product_Interface) {
                $shops = $product->get_default_variant()->get_shops();
            } elseif($product instanceof Shop_Aware_Product_Interface) {
                $shops = $product->get_shops();
            } else {
                continue;
            }

            foreach ($shops as $shop) {
                if($current == $limit) {
                    break;
                }

                $shop_template = $shop->get_template();
                if($shop_template === null) {
                    continue;
                }

                $provider = $shop_template->get_provider();
                if($provider === null) {
                    continue;
                }

                $affiliate_id = $shop->get_affiliate_id();
                if($affiliate_id === null) {
                    continue;
                }

                if($provider->get_name()->get_value() === self::PROVIDER) {
                    $affiliate_ids[$affiliate_id->get_value()] = $affiliate_id;
                    $current++;
                }
            }
        }

        return array_values($affiliate_ids);
    }

    /**
     * Make multiple Amazon Item Lookups.
     *
     * @since 0.7
     * @param Amazon_Provider $provider
     * @param Affiliate_Id[] $affiliate_ids
     * @return null|array
     */
    protected function item_lookups(Amazon_Provider $provider, $affiliate_ids)
    {
        $raw_affiliate_ids = array();
        foreach($affiliate_ids as $affiliate_id) {
            $raw_affiliate_ids[] = $affiliate_id->get_value();
        }

        $conf = new GenericConfiguration();
        $client = new Client();
        $request = new GuzzleRequest($client);

        $conf
            ->setCountry($provider->get_country()->get_value())
            ->setAccessKey($provider->get_access_key()->get_value())
            ->setSecretKey($provider->get_secret_key()->get_value())
            ->setAssociateTag($provider->get_associate_tag()->get_value())
            ->setRequest($request);

        $lookup = new Lookup();
        $lookup->setItemId(implode(',', $raw_affiliate_ids));
        $lookup->setResponseGroup(array('Large'));

        $apaiIO = new ApaiIO($conf);
        $formattedResponse = $apaiIO->runOperation($lookup);

        if(empty($formattedResponse)) {
            return null;
        }

        $response = simplexml_load_string($formattedResponse);
        $response = json_encode($response);
        $response = json_decode($response, true);

        return $response;
    }

    /**
     * Map the Amazon Item Lookup Response to the update results.
     *
     * @since 0.7
     * @param array $response
     * @return array
     */
    protected function map_response_to_results($response)
    {
        $result = array();

        $items = $this->find_items($response);
        foreach ($items as $item) {
            $result[] = array(
                'affiliate_id' => $this->find_affiliate_id($item),
                'availability' => $this->find_availability($item),
                'price' => $this->find_price($item),
            );
        }

        return $result;
    }

    /**
     * Apply the results to the products.
     *
     * @since 0.7
     * @param array $results
     * @param Product_Interface[] $products
     * @param string $update_interval
     */
    protected function apply_results_to_products($results, $products, $update_interval)
    {
        foreach ($results as $result) {
            /** @var Affiliate_Id $affiliate_id */
            $affiliate_id = $result['affiliate_id'];
            if($affiliate_id === null) {
                continue;
            }

            foreach ($products as $product) {
                if(!($product instanceof Shop_Aware_Product_Interface)) {
                    continue;
                }

                $shops = $product->get_shops();
                if(empty($shops)) {
                    continue;
                }

                foreach ($shops as $shop) {
                    if($affiliate_id->is_equal_to($shop->get_affiliate_id())) {

                        if($result['availability'] !== null && $this->should_update_availability($update_interval)) {
                            $shop->set_availability($result['availability']);
                            $shop->set_updated_at((new \DateTimeImmutable())->setTimestamp(current_time('timestamp')));
                        }

                        if($result['price'] !== null && $this->should_update_price($update_interval)) {
                            $shop->set_price($result['price']);
                            $shop->set_updated_at((new \DateTimeImmutable())->setTimestamp(current_time('timestamp')));
                        }

                        if($shop->get_availability()->is_out_of_stock() && $this->should_update_price($update_interval)) {
                            $shop->set_price(null);
                            $shop->set_updated_at((new \DateTimeImmutable())->setTimestamp(current_time('timestamp')));
                        }
                    }
                }
            }
        }
    }

    /**
     * Find the items in the response.
     *
     * @since 0.7
     * @param array $response
     * @return array
     */
    protected function find_items($response)
    {
        $items = array();

        if(isset($response['Items']['Item'])) {
            $item = $response['Items']['Item'];

            // Request contains multiple responses
            if(isset($item[0])) {
                $items = $item;

            }

            // Request contains a single response
            if(isset($item['ASIN'])) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * Find the affiliate ID in the item response.
     *
     * @since 0.7
     * @param array $item
     * @return null|Affiliate_Id
     */
    protected function find_affiliate_id($item)
    {
        $affiliate_id = null;

        if(isset($item['ASIN'])) {
            $asin = $item['ASIN'];
            $affiliate_id = new Affiliate_Id($asin);
        }

        return $affiliate_id;
    }

    /**
     * Find the availability in the item response.
     *
     * @since 0.7
     * @param array $item
     * @return null|Availability
     */
    protected function find_availability($item)
    {
        $availability = null;

        if(isset($item['Offers']['TotalOffers'])) {
            $total_offers = intval($item['Offers']['TotalOffers']);

            if($total_offers > 0) {
                $availability = Availability::available();
            } else {
                $availability = Availability::out_of_stock();
            }
        }

        return $availability;
    }

    /**
     * Find the price in the item response.
     *
     * @since 0.7
     * @param array $item
     * @return null|Money
     */
    protected function find_price($item)
    {
        $price = null;

        if(isset($item['Offers']['Offer']['OfferListing']['Money'])) {
            $price = $item['Offers']['Offer']['OfferListing']['Money'];

            if(isset($price['Amount']) && isset($price['CurrencyCode'])) {
                $amount = floatval($price['Amount']) / 100;
                $currency = $price['CurrencyCode'];
                $price = new Money($amount, new Currency($currency));
            }
        }

        return $price;
    }

    /**
     * Check if we have to update the availability.
     *
     * @since 0.7
     * @param string $update_interval
     * @return bool
     */
    protected function should_update_availability($update_interval)
    {
        $availability_update_interval = carbon_get_theme_option(Amazon_Options::AVAILABILITY_UPDATE_INTERVAL);
        if(empty($availability_update_interval) || $availability_update_interval === 'none') {
            return false;
        }

        $should_update = $availability_update_interval === $update_interval;

        return $should_update;
    }

    /**
     * Check if we have to update the price.
     *
     * @since 0.7
     * @param string $update_interval
     * @return bool
     */
    protected function should_update_price($update_interval)
    {
        $price_update_interval = carbon_get_theme_option(Amazon_Options::PRICE_UPDATE_INTERVAL);
        if(empty($price_update_interval) || $price_update_interval === 'none') {
            return false;
        }

        $should_update = $price_update_interval === $update_interval;

        return $should_update;
    }
}
