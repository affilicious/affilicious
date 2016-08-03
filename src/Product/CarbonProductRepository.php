<?php
namespace Affilicious\ProductsPlugin\Product;

use Affilicious\ProductsPlugin\Product\Field\FieldGroup;

class CarbonProductRepository implements ProductRepositoryInterface
{
    const PRICE_COMPARISON_DEFAULT_POSITION = 'affilicious_price_comparison_default_position';
    const PRICE_COMPARISON_EAN = 'affilicious_price_comparison_ean';
    const PRICE_COMPARISON_SHOPS = 'affilicious_price_comparison_shops';

    /**
     * @inheritdoc
     */
    public function store(Product $product)
    {

    }

    /**
     * @inheritdoc
     */
    public function delete($productId)
    {
        wp_delete_post($productId);
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

        // Setup the price comparison
        $defaultPosition = carbon_get_post_meta($post->ID, self::PRICE_COMPARISON_DEFAULT_POSITION);
        $priceComparison = new PriceComparison($defaultPosition);

        $ean = carbon_get_post_meta($post->ID, self::PRICE_COMPARISON_EAN);
        if (!empty($ean)) {
            $priceComparison->setEan($ean);
        }

        $shops = carbon_get_post_meta($post->ID, self::PRICE_COMPARISON_SHOPS, 'complex');
        if (!empty($shops)) {
            $priceComparison->setShops($shops);
        }

        $product->setPriceComparison($priceComparison);

        return $product;
    }

    /**
     * Convert the product into a Wordpress post
     * @param Product $product
     * @return \WP_Post
     */
    private function toPost(Product $product)
    {



    }
}
