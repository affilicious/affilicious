<?php
namespace Affilicious\ProductsPlugin\Product\Infrastructure\Persistence\Carbon;

use Affilicious\ProductsPlugin\Product\Domain\Model\DetailGroup;
use Affilicious\ProductsPlugin\Product\Domain\Model\DetailGroupRepositoryInterface;
use Affilicious\ProductsPlugin\Product\Domain\Model\Product;
use Affilicious\ProductsPlugin\Product\Domain\Model\ProductRepositoryInterface;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class CarbonProductRepository implements ProductRepositoryInterface
{
    const PRODUCT_EAN = 'affilicious_product_ean';
    const PRODUCT_SHOPS = 'affilicious_product_shops';
    const PRODUCT_DETAIL_GROUPS = 'affilicious_product_detail_groups';
    const PRODUCT_RELATED_PRODUCTS = 'affilicious_product_related_products';
    const PRODUCT_RELATED_ACCESSORIES = 'affilicious_product_related_accessories';
    const PRODUCT_RELATED_POSTS = 'affilicious_product_related_posts';

    /**2
     * @var DetailGroupRepositoryInterface
     */
    private $detailGroupRepository;

    /**
     * CarbonProductRepository constructor.
     */
    public function __construct()
    {
        $this->detailGroupRepository = new CarbonDetailGroupRepository();
    }

    /**
     * @inheritdoc
     */
    public function findById($productId)
    {
        // The product ID is just a simple post ID, since the product is just a custom post type
        $post = get_post($productId);
        if ($post === null) {
            return null;
        }

        $product = self::fromPost($post);
        return $product;
    }

    /**
     * @inheritdoc
     */
    public function findAll()
    {
        $query = new \WP_Query(array(
            'post_type' => Product::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        $products = array();
        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $product = self::fromPost($query->post);
                $products[] = $product;
            }

            wp_reset_postdata();
        }

        return $products;
    }

    /**
     * Convert the Wordpress post into a product
     * @param \WP_Post $post
     * @return Product
     */
    private function fromPost(\WP_Post $post)
    {
        $product = new Product($post);

        $ean = carbon_get_post_meta($post->ID, self::PRODUCT_EAN);
        if (!empty($ean)) {
            $product->setEan($ean);
        }

        $shops = carbon_get_post_meta($post->ID, self::PRODUCT_SHOPS, 'complex');
        if (!empty($shops)) {
            $shops = array_map(function($shop) {
                return array(
                    'affiliate_id' => !empty($shop['affiliate_id']) ? $shop['affiliate_id'] : null,
                    'affiliate_link' => !empty($shop['affiliate_link']) ? $shop['affiliate_link'] : null,
                    'currency' => !empty($shop['currency']) ? $shop['currency'] : null,
                    'old_price' => !empty($shop['old_price']) ? floatval($shop['old_price']) : null,
                    'price' => !empty($shop['price']) ? floatval($shop['price']) : null,
                    'shop_id' => !empty($shop['shop_id']) ? intval($shop['shop_id']) : null,
                );
            }, $shops);

            $product->setShops($shops);
        }

        $detailGroups = carbon_get_post_meta($post->ID, self::PRODUCT_DETAIL_GROUPS, 'complex');
        if (!empty($detailGroups)) {
            $result = array();
            foreach ($detailGroups as $detailGroup) {
                $detailGroupId = intval($detailGroup[DetailGroup::DETAIL_ID]);
                $detailGroupObject = $this->detailGroupRepository->findById($detailGroupId);

                $temp = array();
                $temp[Product::DETAIL_GROUP_ID] = $detailGroupId;
                $temp[Product::DETAIL_GROUP_FIELDS] = array_map(function($detail) use ($detailGroup, $detailGroupId) {
                    unset($detail['_type'], $detail[DetailGroup::DETAIL_DEFAULT_VALUE], $detail[DetailGroup::DETAIL_HELP_TEXT]);
                    $detail[Product::DETAIL_VALUE] = $detailGroup[$detail[DetailGroup::DETAIL_KEY]];
                    $detail[Product::DETAIL_VALUE] = $detail[Product::DETAIL_TYPE] === DetailGroup::DETAIL_TYPE_NUMBER ? intval($detail[Product::DETAIL_VALUE]) : $detail[Product::DETAIL_VALUE];
                    return $detail;
                }, $detailGroupObject->getDetails());

                $result[] = $temp;
            }

            $product->setDetailGroups($result);
        }

        $relatedProducts = carbon_get_post_meta($post->ID, self::PRODUCT_RELATED_PRODUCTS);
        if (!empty($relatedProducts)) {
            $relatedProducts = array_map(function ($value) {
                return intval($value);
            }, $relatedProducts);

            $product->setRelatedProducts($relatedProducts);
        }

        $relatedAccessories = carbon_get_post_meta($post->ID, self::PRODUCT_RELATED_ACCESSORIES);
        if (!empty($relatedAccessories)) {
            $relatedAccessories = array_map(function ($value) {
                return intval($value);
            }, $relatedAccessories);

            $product->setRelatedAccessories($relatedAccessories);
        }

        $relatedPosts = carbon_get_post_meta($post->ID, self::PRODUCT_RELATED_POSTS);
        if (!empty($relatedPosts)) {
            $relatedPosts = array_map(function ($value) {
                return intval($value);
            }, $relatedPosts);

            $product->setRelatedPosts($relatedPosts);
        }

        return $product;
    }
}
