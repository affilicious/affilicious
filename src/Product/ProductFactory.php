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
