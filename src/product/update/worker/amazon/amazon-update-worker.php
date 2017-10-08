<?php
namespace Affilicious\Product\Update\Worker\Amazon;

use Affilicious\Common\Helper\Image_Helper;
use Affilicious\Common\Model\Image;
use Affilicious\Product\Helper\Amazon_Helper;
use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Product_Id;
use Affilicious\Product\Model\Shop_Aware_Interface;
use Affilicious\Product\Repository\Product_Repository_Interface;
use Affilicious\Product\Update\Configuration\Configuration;
use Affilicious\Product\Update\Task\Batch_Update_Task;
use Affilicious\Product\Update\Update_Timer;
use Affilicious\Product\Update\Worker\Update_Worker_Interface;
use Affilicious\Provider\Admin\Options\Amazon_Options;
use Affilicious\Provider\Model\Amazon\Amazon_Provider;
use Affilicious\Provider\Repository\Provider_Repository_Interface;
use Affilicious\Shop\Model\Affiliate_Link;
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
    const MAX_UPDATES = 10;

    /**
     * @var Product_Repository_Interface
     */
    protected $product_repository;

    /**
     * @var Shop_Template_Repository_Interface
     */
    protected $shop_template_repository;

    /**
     * @var Provider_Repository_Interface
     */
    protected $provider_repository;

    /**
     * @since 0.8
     * @param Product_Repository_Interface $product_repository
     * @param Shop_Template_Repository_Interface $shop_template_repository
     * @param Provider_Repository_Interface $provider_repository
     */
    public function __construct(
        Product_Repository_Interface $product_repository,
        Shop_Template_Repository_Interface $shop_template_repository,
        Provider_Repository_Interface $provider_repository
    ) {
        $this->product_repository = $product_repository;
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
    public function configure(Configuration $configuration)
    {
        $configuration->set_all([
            'provider_slug' => self::PROVIDER,
            'max_updates' => self::MAX_UPDATES,
        ]);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function execute(Batch_Update_Task $batch_update_task, $update_interval)
    {
        // Get the provider containing the credentials.
        $provider = $batch_update_task->get_provider();
        if(!($provider instanceof Amazon_Provider)) {
            return;
        }

        // Get all products for the update.
        $products = $batch_update_task->get_products();
        if(empty($products)) {
            return;
        }

        // Find the affiliate product IDs for the Amazon API batch item lookups.
        $affiliate_product_ids = $this->find_affiliate_product_ids($products, self::MAX_UPDATES);
        if(empty($affiliate_product_ids)) {
            return;
        }

        // Make a Amazon API batch item lookup based on the affiliate IDs.
        $results = $this->batch_item_lookup($provider, $affiliate_product_ids);
        if(empty($results)) {
            return;
        }

        // Apply the updated information's to the products.
        $this->update_products($update_interval, $results, $products);
    }

    /**
     * Find the given number of affiliate product IDs from the products.
     *
     * @since 0.9.8
     * @param Product[] $products The products for the affiliate product ID extraction.
     * @param int $limit The max limit to find.
     * @return Affiliate_Product_Id[]
     */
    protected function find_affiliate_product_ids(array $products, $limit)
    {
        $current = 0;

        $affiliate_product_ids = array();
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

                $affiliate_product_id = $shop->get_tracking()->get_affiliate_product_id();
                if($affiliate_product_id === null) {
                    continue;
                }

                if($provider->get_slug()->get_value() === self::PROVIDER) {
                    $affiliate_product_ids[$product->get_id()->get_value()] = $affiliate_product_id;
                    $current++;
                }
            }
        }

        return $affiliate_product_ids;
    }

    /**
     * Make one Amazon API batch item lookup.
     *
     * @since 0.9.8
     * @param Amazon_Provider $provider The Amazon provider which holds the credentials.
     * @param Affiliate_Product_Id[] $affiliate_product_ids The affiliate IDs for the batch item lookup.
     * @return null|array The Amazon API response as an array.
     */
    protected function batch_item_lookup(Amazon_Provider $provider, array $affiliate_product_ids)
    {
    	// Convert the affiliate product IDs to unique item IDs.
        $items_ids = array();
        foreach($affiliate_product_ids as $affiliate_product_id) {
            $items_ids[$affiliate_product_id->get_value()] = $affiliate_product_id->get_value();
        }

	    $items_ids = implode(',', $items_ids);

        // Build the Amazon item lookup request.
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
        $lookup->setItemId($items_ids);
        $lookup->setResponseGroup(['Large']);

        // Do the Amazon batch item lookup request.
        $apaiIO = new ApaiIO($conf);
        $response = $apaiIO->runOperation($lookup);
        if(empty($response)) {
            return null;
        }

        // Convert the items into results.
	    $results = [];

	    foreach ($affiliate_product_ids as $product_id => $affiliate_product_id) {
		    $items = $this->find_items($response);
		    foreach ($items as $item) {
			    if($affiliate_product_id->is_equal_to(Amazon_Helper::find_affiliate_product_id($item))) {
				    $results[$product_id] = array(
					    'affiliate_product_id' => Amazon_Helper::find_affiliate_product_id($item),
					    'affiliate_link' => Amazon_Helper::find_affiliate_link($item),
					    'thumbnail' => Amazon_Helper::find_thumbnail($item),
					    'image_gallery' => Amazon_Helper::find_image_gallery($item),
					    'price' => Amazon_Helper::find_price($item),
					    'old_price' => Amazon_Helper::find_old_price($item),
					    'availability' => Amazon_Helper::find_availability($item),
				    );
			    }
		    }
	    }

	    return $results;
    }

    /**
     * Update the products with the help of the results.
     *
     * @since 0.9.8
     * @param string $update_interval The current update interval from the cron job.
     * @param array $results The results which can be applied to the products.
     * @param Product[] $products The products where the results can be applied to.
     */
    protected function update_products($update_interval, array $results, array $products)
    {
        foreach ($results as $product_id => $result) {
            $affiliate_product_id = $result['affiliate_product_id'];
            if($affiliate_product_id instanceof Affiliate_Product_Id) {
	            foreach ($products as $product) {
	            	if(!$product->get_id()->is_equal_to(new Product_Id($product_id))) {
	            		continue;
		            }

	                if($this->should_update_thumbnail($update_interval, $product)) {
	                    $this->update_thumbnail($result['thumbnail'], $product);
	                }

	                if(!empty($this->should_update_image_gallery($update_interval, $product))) {
	                    $this->update_image_gallery($result['image_gallery'], $product);
	                }

		            if($product instanceof Shop_Aware_Interface) {
			            $shops = $product->get_shops();
			            foreach ($shops as $shop) {
				            if ($affiliate_product_id->is_equal_to($shop->get_tracking()->get_affiliate_product_id())) {
					            if ($this->should_update_availability($update_interval, $product, $shop)) {
						            $this->update_availability($result['availability'], $product, $shop );
					            }

					            if ($this->should_update_affiliate_link($update_interval, $product, $shop)) {
						            $this->update_affiliate_link($result['affiliate_link'], $product, $shop);
					            }

					            if ($this->should_update_price($update_interval, $product, $shop)) {
						            $this->update_price($result['price'], $product, $shop);
					            }

					            if ($this->should_update_old_price($update_interval, $product, $shop)) {
						            $this->update_old_price($result['old_price'], $product, $shop);
					            }
				            }
			            }
		            }
	            }
            }
        }

	    // Store all updated products.
	    foreach ($products as $product) {
		    $this->product_repository->store($product);
	    }
    }

    /**
     * Update the product thumbnail.
     *
     * @since 0.9
     * @param Image|null $thumbnail The new thumbnail for the update.
     * @param Product $product The current product to update.
     */
    protected function update_thumbnail(Image $thumbnail = null, Product $product)
    {
        do_action('aff_product_amazon_update_worker_before_update_thumbnail', $thumbnail, $product);

        $current_thumbnail = $product->get_thumbnail();
        if($current_thumbnail !== null) {
            Image_Helper::delete($current_thumbnail, true);
        }

        $product->set_thumbnail($thumbnail);
        $product->set_updated_at((new \DateTimeImmutable())->setTimestamp(current_time('timestamp')));

        do_action('aff_product_amazon_update_worker_after_update_thumbnail', $thumbnail, $product);
    }

    /**
     * Update the product image gallery.
     *
     * @since 0.9
     * @param array $image_gallery The image gallery for the update.
     * @param Product $product The current product to update.
     */
    protected function update_image_gallery(array $image_gallery = [], Product $product)
    {
        do_action('aff_product_amazon_update_worker_before_update_image_gallery', $image_gallery, $product);

        $current_image_gallery = $product->get_image_gallery();
        foreach ($current_image_gallery as $current_image) {
            Image_Helper::delete($current_image, true);
        }

        $image_gallery = apply_filters('aff_product_amazon_update_worker_update_price', $image_gallery, $product);
        $product->set_image_gallery($image_gallery);
        $product->set_updated_at((new \DateTimeImmutable())->setTimestamp(current_time('timestamp')));

        do_action('aff_product_amazon_update_worker_after_update_image_gallery', $image_gallery, $product);
    }

    /**
     * Update the shop price in the product.
     *
     * @since 0.9
     * @param Money $price|null The new price for the update.
     * @param Product $product The current product to update.
     * @param Shop $shop The current shop to update.
     */
    protected function update_price(Money $price = null, Product $product, Shop $shop)
    {
        do_action('aff_product_amazon_update_worker_before_update_price', $price, $product, $shop);

        if($shop->get_pricing()->get_availability()->is_out_of_stock()) {
            $price = null;
        }

        $price = apply_filters('aff_product_amazon_update_worker_update_price', $price, $product, $shop);
        $shop->get_pricing()->set_price($price);
        $shop->set_updated_at((new \DateTimeImmutable())->setTimestamp(current_time('timestamp')));
	    $product->set_updated_at((new \DateTimeImmutable())->setTimestamp(current_time('timestamp')));

        do_action('aff_product_amazon_update_worker_after_update_price', $price, $product, $shop);
    }

    /**
     * Update the shop old price in the product.
     *
     * @since 0.9
     * @param Money $old_price|null The new old price for the update.
     * @param Product $product The current product to update.
     * @param Shop $shop The current shop to update.
     */
    protected function update_old_price(Money $old_price = null, Product $product, Shop $shop)
    {
        do_action('aff_product_amazon_update_worker_before_update_old_price', $old_price, $product, $shop);

        if($shop->get_pricing()->get_availability()->is_out_of_stock()) {
            $old_price = null;
        }

        $old_price = apply_filters('aff_product_amazon_update_worker_update_old_price', $old_price, $product, $shop);
        $shop->get_pricing()->set_old_price($old_price);
        $shop->set_updated_at((new \DateTimeImmutable())->setTimestamp(current_time('timestamp')));
	    $product->set_updated_at((new \DateTimeImmutable())->setTimestamp(current_time('timestamp')));

        do_action('aff_product_amazon_update_worker_after_update_old_price', $old_price, $product, $shop);
    }

    /**
     * Update the shop availability in the product.
     *
     * @since 0.9
     * @param Availability|null $availability The new availability for the update.
     * @param Product $product The current product to update.
     * @param Shop $shop The current shop to update.
     */
    protected function update_availability(Availability $availability = null, Product $product, Shop $shop)
    {
        do_action('aff_product_amazon_update_worker_before_update_availability', $availability, $product, $shop);

        $availability = apply_filters('aff_product_amazon_update_worker_update_availability', $availability, $product, $shop);
        $shop->get_pricing()->set_availability($availability);
        $shop->set_updated_at((new \DateTimeImmutable())->setTimestamp(current_time('timestamp')));
	    $product->set_updated_at((new \DateTimeImmutable())->setTimestamp(current_time('timestamp')));

        do_action('aff_product_amazon_update_worker_before_update_availability', $availability, $product, $shop);
    }

	/**
	 * Update the shop affiliate link in the product.
	 *
	 * @since 0.9.8
	 * @param Affiliate_Link|null $affiliate_link
	 * @param Product $product
	 * @param Shop $shop
	 */
    protected function update_affiliate_link(Affiliate_Link $affiliate_link = null, Product $product, Shop $shop)
    {
	    do_action('aff_product_amazon_update_worker_before_update_affiliate_link', $affiliate_link, $product, $shop);

	    $affiliate_link = apply_filters('aff_product_amazon_update_worker_update_affiliate_link', $affiliate_link, $product, $shop);

	    if($affiliate_link !== null) {
		    $shop->get_tracking()->set_affiliate_link( $affiliate_link );
	    }

	    $shop->set_updated_at((new \DateTimeImmutable())->setTimestamp(current_time('timestamp')));
	    $product->set_updated_at((new \DateTimeImmutable())->setTimestamp(current_time('timestamp')));

	    do_action('aff_product_amazon_update_worker_before_update_affiliate_link', $affiliate_link, $product, $shop);
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
	 * Check if we have to update the shop affiliate links in the products.
	 *
	 * @since 0.9.8
	 * @param string $update_interval The current update interval from the cron job.
	 * @param Product $product The current product to update.
	 * @param Shop $shop The current shop to update.
	 * @return bool Whether to update the shop price or not.
	 */
	protected function should_update_affiliate_link($update_interval, Product $product, Shop $shop)
	{
		$price_update_interval = carbon_get_theme_option(Amazon_Options::AFFILIATE_LINK_UPDATE_INTERVAL);
		$should_update = $this->should_update($update_interval, $price_update_interval);
		$should_update = apply_filters('aff_product_amazon_update_worker_should_update_affiliate_link', $should_update, $update_interval, $product, $shop);

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
