<?php
namespace Affilicious\Product\Update\Worker;

use Affilicious\Common\Helper\Image_Helper;
use Affilicious\Common\Model\Image_Id;
use Affilicious\Product\Helper\Amazon_Helper;
use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Shop_Aware_Interface;
use Affilicious\Product\Update\Configuration\Configuration;
use Affilicious\Product\Update\Task\Batch_Update_Task_Interface;
use Affilicious\Product\Update\Update_Timer;
use Affilicious\Provider\Model\Amazon\Amazon_Provider;
use Affilicious\Provider\Options\Amazon_Options;
use Affilicious\Provider\Repository\Provider_Repository_Interface;
use Affilicious\Shop\Model\Affiliate_Product_Id;
use Affilicious\Shop\Model\Availability;
use Affilicious\Shop\Model\Money;
use Affilicious\Shop\Model\Shop;
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

class Amazon_Update_Worker implements Update_Worker_Interface
{
    const NAME = 'amazon';
    const PROVIDER = 'amazon';
    const UPDATE_INTERVAL = 'hourly';
    const MIN_UPDATES = 1;
    const MAX_UPDATES = 10;

    /**
     * @var Shop_Template_Repository_Interface
     */
    private $shop_template_repository;

    /**
     * @var Provider_Repository_Interface
     */
    private $provider_repository;

    /**
     * @since 0.8
     * @param Shop_Template_Repository_Interface $shop_template_repository
     * @param Provider_Repository_Interface $provider_repository
     */
    public function __construct(
        Shop_Template_Repository_Interface $shop_template_repository,
        Provider_Repository_Interface $provider_repository
    )
    {
        $this->shop_template_repository = $shop_template_repository;
        $this->provider_repository = $provider_repository;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function get_name()
    {
        return self::NAME;
    }

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

        $affiliate_ids = $this->extract_affiliate_product_ids($products, self::MAX_UPDATES);
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

        $this->apply_results_to_products($update_interval, $results, $products);
    }

    /**
     * Extract the given number of affiliate product IDs from the products.
     *
     * @since 0.7
     * @param Product[] $products The products for the affiliate product ID extraction.
     * @param int $limit The max limit for the extraction.
     * @return Affiliate_Product_Id[]
     */
    protected function extract_affiliate_product_ids(array $products, $limit)
    {
        $current = 0;

        $affiliate_ids = array();
        foreach ($products as $product) {

            if($product instanceof Complex_Product) {
                $shops = $product->get_default_variant()->get_shops();
            } elseif($product instanceof Shop_Aware_Interface) {
                $shops = $product->get_shops();
            } else {
                continue;
            }

            foreach ($shops as $shop) {
                if($current == $limit) {
                    break;
                }

                if(!$shop->has_template_id()) {
                    continue;
                }

                $shop_template = $this->shop_template_repository->find_one_by_id($shop->get_template_id());
                if($shop_template === null) {
                    continue;
                }

                if(!$shop_template->has_provider_id()) {
                    continue;
                }

                $provider = $this->provider_repository->find_one_by_id($shop_template->get_provider_id());
                if($provider === null) {
                    continue;
                }

                $affiliate_id = $shop->get_tracking()->get_affiliate_id();
                if($affiliate_id === null) {
                    continue;
                }

                if($provider->get_slug()->get_value() === self::PROVIDER) {
                    $affiliate_ids[$affiliate_id->get_value()] = $affiliate_id;
                    $current++;
                }
            }
        }

        return array_values($affiliate_ids);
    }

    /**
     * Make multiple Amazon Item lookups.
     *
     * @since 0.7
     * @param Amazon_Provider $provider The Amazon provider which holds the credentials.
     * @param Affiliate_Product_Id[] $affiliate_ids The affiliate IDs for the lookup.
     * @return null|array The Amazon API response as an array.
     */
    protected function item_lookups(Amazon_Provider $provider, array $affiliate_ids)
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
            ->setRequest($request)
            ->setResponseTransformer(new XmlToArray());

        $lookup = new Lookup();
        $lookup->setItemId(implode(',', $raw_affiliate_ids));
        $lookup->setResponseGroup(array('Large'));

        $apaiIO = new ApaiIO($conf);
        $response = $apaiIO->runOperation($lookup);

        if(empty($response)) {
            return null;
        }

        return $response;
    }

    /**
     * Map the Amazon Item Lookup response to the update results.
     *
     * @since 0.7
     * @param array $response The Amazon API response.
     * @return array The results which can be applied to the products.
     */
    protected function map_response_to_results($response)
    {
        $result = array();

        $items = $this->find_items($response);
        foreach ($items as $item) {
            $result[] = array(
                'affiliate_product_id' => Amazon_Helper::find_affiliate_product_id($item),
                'thumbnail_id' => Amazon_Helper::find_thumbnail_id($item),
                'image_gallery_ids' => Amazon_Helper::find_image_gallery_ids($item),
                'price' => Amazon_Helper::find_price($item),
                'old_price' => Amazon_Helper::find_old_price($item),
                'availability' => Amazon_Helper::find_availability($item),
            );
        }

        return $result;
    }

    /**
     * Apply the Amazon API results to the products.
     *
     * @since 0.9
     * @param string $update_interval The current update interval from the cron job.
     * @param array $results The results which can be applied to the products.
     * @param Product[] $products The products where the results can be applied to.
     */
    protected function apply_results_to_products($update_interval, array $results, array $products)
    {
        foreach ($results as $result) {
            $affiliate_product_id = $result['affiliate_product_id'];
            if($affiliate_product_id === null || !($affiliate_product_id instanceof Affiliate_Product_Id)) {
                continue;
            }

            foreach ($products as $product) {
                if(!($product instanceof Shop_Aware_Interface) || !$product->has_shops()) {
                    continue;
                }

                if($result['thumbnail_id'] !== null && $this->should_update_thumbnail($update_interval, $product)) {
                    $this->update_thumbnail($result['thumbnail_id'], $product);
                }

                if(!empty($result['image_gallery_ids']) && $this->should_update_image_gallery($update_interval, $product)) {
                    $this->update_image_gallery($result['image_gallery_ids'], $product);
                }

                $shops = $product->get_shops();
                foreach ($shops as $shop) {
                    if($affiliate_product_id->is_equal_to($shop->get_tracking()->get_affiliate_id())) {
                        if($result['availability'] !== null && $this->should_update_availability($update_interval, $product, $shop)) {
                            $this->update_availability($result['availability'], $product, $shop);
                        }

                        if($result['price'] !== null && $this->should_update_price($update_interval, $product, $shop)) {
                            $this->update_price($result['price'], $product, $shop);
                        }

                        if($result['old_price'] !== null && $this->should_update_old_price($update_interval, $product, $shop)) {
                            $this->update_old_price($result['old_price'], $product, $shop);
                        }
                    }
                }
            }
        }
    }

    /**
     * Update the product thumbnail.
     *
     * @since 0.9
     * @param Image_Id $thumbnail_id The new thumbnail ID for the update.
     * @param Product $product The current product to update.
     */
    protected function update_thumbnail(Image_Id $thumbnail_id, Product $product)
    {
        do_action('aff_product_amazon_update_worker_before_update_thumbnail', $thumbnail_id, $product);

        $current_thumbnail_id = $product->get_thumbnail_id();
        if($current_thumbnail_id !== null) {
            Image_Helper::delete($current_thumbnail_id, true);
        }

        $product->set_thumbnail_id($thumbnail_id);
        $product->set_updated_at((new \DateTimeImmutable())->setTimestamp(current_time('timestamp')));

        do_action('aff_product_amazon_update_worker_after_update_thumbnail', $thumbnail_id, $product);
    }

    /**
     * Update the product image gallery.
     *
     * @since 0.9
     * @param array $image_gallery_ids The image gallery IDs for the update.
     * @param Product $product The current product to update.
     */
    protected function update_image_gallery(array $image_gallery_ids, Product $product)
    {
        do_action('aff_product_amazon_update_worker_before_update_image_gallery', $image_gallery_ids, $product);

        $current_image_gallery_ids = $product->get_image_gallery();
        foreach ($current_image_gallery_ids as $current_image_gallery_id) {
            Image_Helper::delete($current_image_gallery_id, true);
        }

        $image_gallery_ids = apply_filters('aff_product_amazon_update_worker_update_price', $image_gallery_ids, $product);
        $product->set_image_gallery($image_gallery_ids);
        $product->set_updated_at((new \DateTimeImmutable())->setTimestamp(current_time('timestamp')));

        do_action('aff_product_amazon_update_worker_after_update_image_gallery', $image_gallery_ids, $product);
    }

    /**
     * Update the shop price in the product.
     *
     * @since 0.9
     * @param Money $price The new price for the update.
     * @param Product $product The current product to update.
     * @param Shop $shop The current shop to update.
     */
    protected function update_price(Money $price, Product $product, Shop $shop)
    {
        do_action('aff_product_amazon_update_worker_before_update_price', $price, $product, $shop);

        if($shop->get_pricing()->get_availability()->is_out_of_stock()) {
            $price = null;
        }

        $price = apply_filters('aff_product_amazon_update_worker_update_price', $price, $product, $shop);
        $shop->get_pricing()->set_price($price);
        $shop->set_updated_at((new \DateTimeImmutable())->setTimestamp(current_time('timestamp')));

        do_action('aff_product_amazon_update_worker_after_update_price', $price, $product, $shop);
    }

    /**
     * Update the shop old price in the product.
     *
     * @since 0.9
     * @param Money $old_price The new old price for the update.
     * @param Product $product The current product to update.
     * @param Shop $shop The current shop to update.
     */
    protected function update_old_price(Money $old_price, Product $product, Shop $shop)
    {
        do_action('aff_product_amazon_update_worker_before_update_old_price', $old_price, $product, $shop);

        if($shop->get_pricing()->get_availability()->is_out_of_stock()) {
            $old_price = null;
        }

        $old_price = apply_filters('aff_product_amazon_update_worker_update_old_price', $old_price, $product, $shop);
        $shop->get_pricing()->set_old_price($old_price);
        $shop->set_updated_at((new \DateTimeImmutable())->setTimestamp(current_time('timestamp')));

        do_action('aff_product_amazon_update_worker_after_update_old_price', $old_price, $product, $shop);
    }

    /**
     * Update the shop availability in the product.
     *
     * @since 0.9
     * @param Availability $availability The new availability for the update.
     * @param Product $product The current product to update.
     * @param Shop $shop The current shop to update.
     */
    protected function update_availability(Availability $availability, Product $product, Shop $shop)
    {
        do_action('aff_product_amazon_update_worker_before_update_availability', $availability, $product, $shop);

        $availability = apply_filters('aff_product_amazon_update_worker_update_availability', $availability, $product, $shop);
        $shop->get_pricing()->set_availability($availability);
        $shop->set_updated_at((new \DateTimeImmutable())->setTimestamp(current_time('timestamp')));

        do_action('aff_product_amazon_update_worker_before_update_availability', $availability, $product, $shop);
    }

    /**
     * Check if we have to update the product thumbnails.
     *
     * @since 0.9
     * @param string $update_interval The current update interval from the cron job.
     * @param Product $product The current product to update.
     * @return bool Whether to update the thumbnail or not.
     */
    protected function should_update_thumbnail($update_interval, Product $product)
    {
        $thumbnail_update_interval = carbon_get_theme_option(Amazon_Options::THUMBNAIL_UPDATE_INTERVAL);
        $should_update = $this->should_update($update_interval, $thumbnail_update_interval);
        $should_update = apply_filters('aff_product_amazon_update_worker_should_update_thumbnail', $should_update, $update_interval, $product);

        return $should_update;
    }

    /**
     * Check if we have to update the product image galleries.
     *
     * @since 0.9
     * @param string $update_interval The current update interval from the cron job.
     * @param Product $product The current product to update.
     * @return bool Whether to update the image gallery or not.
     */
    protected function should_update_image_gallery($update_interval, Product $product)
    {
        $image_gallery_update_interval = carbon_get_theme_option(Amazon_Options::IMAGE_GALLERY_UPDATE_INTERVAL);
        $should_update = $this->should_update($update_interval, $image_gallery_update_interval);
        $should_update = apply_filters('aff_product_amazon_update_worker_should_update_image_gallery', $should_update, $update_interval, $product);

        return $should_update;
    }

    /**
     * Check if we have to update the shop prices in the products.
     *
     * @since 0.7
     * @param string $update_interval The current update interval from the cron job.
     * @param Product $product The current product to update.
     * @param Shop $shop The current shop to update.
     * @return bool Whether to update the shop price or not.
     */
    protected function should_update_price($update_interval, Product $product, Shop $shop)
    {
        $price_update_interval = carbon_get_theme_option(Amazon_Options::PRICE_UPDATE_INTERVAL);
        $should_update = $this->should_update($update_interval, $price_update_interval);
        $should_update = apply_filters('aff_product_amazon_update_worker_should_update_price', $should_update, $update_interval, $product, $shop);

        return $should_update;
    }

    /**
     * Check if we have to update the old shop prices in the products.
     *
     * @since 0.7
     * @param string $update_interval The current update interval from the cron job.
     * @param Product $product The current product to update.
     * @param Shop $shop The current shop to update.
     * @return bool Whether to update the old shop price or not.
     */
    protected function should_update_old_price($update_interval, Product $product, Shop $shop)
    {
        $old_price_update_interval = carbon_get_theme_option(Amazon_Options::OLD_PRICE_UPDATE_INTERVAL);
        $should_update = $this->should_update($update_interval, $old_price_update_interval);
        $should_update = apply_filters('aff_product_amazon_update_worker_should_update_old_price', $should_update, $update_interval, $product, $shop);

        return $should_update;
    }

    /**
     * Check if we have to update the shop availabilities in the products.
     *
     * @since 0.7
     * @param string $update_interval The current update interval from the cron job.
     * @param Product $product The current product to update.
     * @param Shop $shop The current shop to update.
     * @return bool Whether to update the shop availability or not.
     */
    protected function should_update_availability($update_interval, Product $product, Shop $shop)
    {
        $availability_update_interval = carbon_get_theme_option(Amazon_Options::AVAILABILITY_UPDATE_INTERVAL);
        $should_update = $this->should_update($update_interval, $availability_update_interval);
        $should_update = apply_filters('aff_product_amazon_update_worker_should_update_availability', $should_update, $update_interval, $product, $shop);

        return $should_update;
    }

    /**
     * Check if the update interval is should be active.
     *
     * @since 0.9
     * @param string $current_update_interval The current update interval from the cron job.
     * @param bool|string $check_update_interval The update interval to check.
     * @return bool Whether to update or not.
     */
    protected function should_update($current_update_interval, $check_update_interval)
    {
        if($check_update_interval === false) {
            $check_update_interval = Update_Timer::HOURLY;
        }

        if(empty($check_update_interval) || $check_update_interval === 'none') {
            return false;
        }

        return $check_update_interval == $current_update_interval;
    }

    /**
     * Find the items in the Amazon API response.
     *
     * @since 0.7
     * @param array $response The Amazon API response containing the product items.
     * @return array The items from the Amazon API response containing the current product information.
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
}
