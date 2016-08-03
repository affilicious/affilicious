<?php
namespace Affilicious\ProductsPlugin\Product\Infrastructure\Persistence\Carbon;

use Affilicious\ProductsPlugin\Product\Domain\Model\FieldGroupRepositoryInterface;
use Affilicious\ProductsPlugin\Product\Domain\Model\Product;
use Affilicious\ProductsPlugin\Product\Domain\Model\ProductRepositoryInterface;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class CarbonProductRepository implements ProductRepositoryInterface
{
    const PRODUCT_EAN = 'affilicious_product_ean';
    const PRODUCT_SHOPS = 'affilicious_product_shops';
    const PRODUCT_FIELD_GROUPS = 'affilicious_product_field_groups';

    /**
     * @var FieldGroupRepositoryInterface
     */
    private $fieldGroupRepository;

    /**
     * CarbonProductRepository constructor.
     */
    public function __construct()
    {
        $this->fieldGroupRepository = new CarbonFieldGroupRepository();
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
            $product->setShops($shops);
        }

        $fieldGroups = carbon_get_post_meta($post->ID, self::PRODUCT_FIELD_GROUPS, 'complex');
        if (!empty($fieldGroups)) {
            $product->setFieldGroups($fieldGroups);
        }

        return $product;
    }
}
