<?php
namespace Affilicious\Shop\Infrastructure\Persistence\Wordpress;

use Affilicious\Product\Domain\Exception\InvalidPostTypeException;
use Affilicious\Shop\Domain\Model\Shop;
use Affilicious\Shop\Domain\Model\ShopId;
use Affilicious\Shop\Domain\Model\ShopRepositoryInterface;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class WordpressShopRepository implements ShopRepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findById(ShopId $id)
    {
        $post = get_post($id->getValue());
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
