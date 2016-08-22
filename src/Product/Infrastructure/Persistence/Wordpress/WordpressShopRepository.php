<?php
namespace Affilicious\Product\Infrastructure\Persistence\Wordpress;

use Affilicious\Product\Domain\Exception\InvalidPostTypeException;
use Affilicious\Product\Domain\Model\Shop;
use Affilicious\Product\Domain\Model\ShopRepositoryInterface;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class WordpressShopRepository implements ShopRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findById($shopId)
    {
        // The product ID is just a simple post ID, since the product is just a custom post type
        $post = get_post($shopId);
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
            'post_type' => Shop::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        $shops = array();
        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $shop = self::fromPost($query->post);
                $shops[] = $shop;
            }

            wp_reset_postdata();
        }

        return $shops;
    }

    /**
     * Convert the Wordpress post into a shop
     *
     * @since 0.3
     * @param \WP_Post $post
     * @return Shop
     * @throws InvalidPostTypeException
     */
    private function fromPost(\WP_Post $post)
    {
        if($post->post_type !== Shop::POST_TYPE) {
            throw new InvalidPostTypeException($post->post_type, Shop::POST_TYPE);
        }

        $shop = new Shop($post);
        return $shop;
    }
}
