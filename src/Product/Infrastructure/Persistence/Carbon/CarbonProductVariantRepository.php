<?php
namespace Affilicious\Product\Infrastructure\Persistence\Carbon;

use Affilicious\Product\Domain\Model\Variant\ProductVariant;
use Affilicious\Product\Domain\Model\Variant\ProductVariantRepositoryInterface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class CarbonProductVariantRepository extends CarbonProductRepository implements ProductVariantRepositoryInterface
{

    /**
     * @inheritdoc
     */
    public function findAll()
    {
        $query = new \WP_Query(array(
            'post_type' => ProductVariant::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        $products = array();
        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $product = parent::buildProductFromPost($query->post);
                $products[] = $product;
            }

            wp_reset_postdata();
        }

        return $products;
    }
}
