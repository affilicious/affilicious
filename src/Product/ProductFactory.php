<?php
namespace Affilicious\ProductsPlugin\Product;

use Affilicious\ProductsPlugin\Product\Detail\DetailGroupFactory;
use Affilicious\ProductsPlugin\Product\Field\FieldGroup;
use Affilicious\ProductsPlugin\Product\Field\FieldGroupFactory;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class ProductFactory
{
    /**
     * @var FieldGroupFactory
     */
    private $fieldGroupFactory;

    /**
     * @var DetailGroupFactory
     */
    private $detailGroupFactory;

    /**
     * ProductFactory constructor.
     */
    public function __construct()
    {
        $this->fieldGroupFactory = new FieldGroupFactory();
        $this->detailGroupFactory = new DetailGroupFactory();
    }

    /**
     * @param \WP_Post $post
     * @return Product
     */
    public function create(\WP_Post $post)
    {
        $product = new Product($post);

        // Setup the price comparison
        $defaultPosition = carbon_get_post_meta($post->ID, CarbonProductRepository::PRICE_COMPARISON_DEFAULT_POSITION);
        $priceComparison = new PriceComparison($defaultPosition);

        $ean = carbon_get_post_meta($post->ID, CarbonProductRepository::PRICE_COMPARISON_EAN);
        if (!empty($ean)) {
            $priceComparison->setEan($ean);
        }

        $shops = carbon_get_post_meta($post->ID, CarbonProductRepository::PRICE_COMPARISON_SHOPS, 'complex');
        if (!empty($shops)) {
            $priceComparison->setShops($shops);
        }

        $product->setPriceComparison($priceComparison);

        $query = new \WP_Query(array(
            'post_type' => FieldGroup::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();

                $fieldGroup = $this->fieldGroupFactory->create($query->post);
                $product->addFieldGroup($fieldGroup);

                $title = $fieldGroup->getTitle();
                if(!empty($title)) {
                    $detailGroup = $this->detailGroupFactory->create($product, $fieldGroup);
                    $product->addDetailGroup($detailGroup);
                }
            }

            wp_reset_postdata();
        }

        return $product;
    }
}
