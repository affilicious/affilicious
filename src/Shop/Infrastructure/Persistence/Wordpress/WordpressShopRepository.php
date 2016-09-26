<?php
namespace Affilicious\Shop\Infrastructure\Persistence\Wordpress;

use Affilicious\Common\Domain\Model\Image\Height;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Image\ImageId;
use Affilicious\Common\Domain\Model\Image\Source;
use Affilicious\Common\Domain\Model\Image\Width;
use Affilicious\Product\Domain\Exception\InvalidPostTypeException;
use Affilicious\Shop\Domain\Model\Shop;
use Affilicious\Shop\Domain\Model\ShopId;
use Affilicious\Shop\Domain\Model\ShopRepositoryInterface;
use Affilicious\Shop\Domain\Model\Title;

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

        // ID, Title
        $shop = new Shop(
            new ShopId($post->ID),
            new Title($post->post_title)
        );

        // Thumbnail
        $thumbnailId = get_post_thumbnail_id($post->ID);
        if (!empty($thumbnailId)) {
            $thumbnail = self::buildImageFromAttachmentId($thumbnailId);

            if($thumbnail !== null) {
                $shop->setThumbnail($thumbnail);
            }
        }

        return $shop;
    }

    /**
     * @since 0.6
     * @param int $attachmentId
     * @return null|Image
     */
    private function buildImageFromAttachmentId($attachmentId)
    {
        $attachment = wp_get_attachment_image_src($attachmentId);
        if(empty($attachment) && count($attachment) == 0) {
            return null;
        }

        $source = $attachment[0];
        if(empty($source)) {
            return null;
        }

        $image = new Image(
            new ImageId($attachmentId),
            new Source($source)
        );

        $width = $attachment[1];
        if(!empty($width)) {
            $image->setWidth(new Width($width));
        }

        $height = $attachment[2];
        if(!empty($height)) {
            $image->setHeight(new Height($height));
        }

        return $image;
    }
}
