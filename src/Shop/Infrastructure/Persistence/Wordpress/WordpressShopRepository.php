<?php
namespace Affilicious\Shop\Infrastructure\Persistence\Wordpress;

use Affilicious\Common\Domain\Exception\InvalidPostTypeException;
use Affilicious\Common\Domain\Model\Image\Height;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Image\ImageId;
use Affilicious\Common\Domain\Model\Image\Source;
use Affilicious\Common\Domain\Model\Image\Width;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Shop\Domain\Exception\MissingShopException;
use Affilicious\Shop\Domain\Model\Shop;
use Affilicious\Shop\Domain\Model\ShopFactoryInterface;
use Affilicious\Shop\Domain\Model\ShopId;
use Affilicious\Shop\Domain\Model\ShopRepositoryInterface;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class WordpressShopRepository implements ShopRepositoryInterface
{
    const THUMBNAIL_ID = '_thumbnail_id';

    /**
     * @var ShopFactoryInterface
     */
    protected $shopFactory;

    /**
     * @since 0.6
     * @param ShopFactoryInterface $shopFactory
     */
    public function __construct(ShopFactoryInterface $shopFactory)
    {
        $this->shopFactory = $shopFactory;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function store(Shop $shop)
    {
        $post = array();
        if($shop->hasId()) {
            $post = get_post($shop->getId()->getValue(), ARRAY_A);
        }

        $args = wp_parse_args(array(
            'post_title' => $shop->getTitle()->getValue(),
            'post_name' => $shop->getName()->getValue(),
            'post_status' => 'publish',
            'post_type' => Shop::POST_TYPE,
        ), $post);

        if($shop->hasId()) {
            $args['ID'] = $shop->getId()->getValue();
        }

        $id = wp_insert_post($args);

        $this->storeThumbnail($shop);

        if(empty($post)) {
            $post = get_post($id, OBJECT);

            $shop->setId(new ShopId($id));
            $shop->setName(new Name($post->post_name));
        }

        return $shop;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function delete(ShopId $shopId)
    {
        $shop = $this->findById($shopId);
        if($shop === null) {
            throw new MissingShopException($shopId);
        }

        $post = wp_delete_post($shopId->getValue(), false);
        if(empty($post)) {
            throw new MissingShopException($shopId);
        }

        $shop->setId(null);

        return $shop;
    }

    /**
     * @inheritdoc
     * @since 0.3
     */
    public function findById(ShopId $id)
    {
        $post = get_post($id->getValue());
        if ($post === null) {
            return null;
        }

        $shop = self::getShopFromPost($post);
        return $shop;
    }

    /**
     * @inheritdoc
     * @since 0.3
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
                $shop = self::getShopFromPost($query->post);
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
    protected function getShopFromPost(\WP_Post $post)
    {
        if($post->post_type !== Shop::POST_TYPE) {
            throw new InvalidPostTypeException($post->post_type, Shop::POST_TYPE);
        }

        // Title, Name, Key
        $shop = $this->shopFactory->create(
            new Title($post->post_title),
            new Name($post->post_name)
        );

        // ID
        $shop->setId(new ShopId($post->ID));

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
    protected function buildImageFromAttachmentId($attachmentId)
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

    /**
     * Store the thumbnail into the post
     *
     * @since 0.6
     * @param Shop $shop
     */
    protected function storeThumbnail(Shop $shop)
    {
        $postId = $shop->getId()->getValue();

        if ($shop->hasThumbnail() && !wp_is_post_revision($postId)) {
            if(!update_post_meta($postId, self::THUMBNAIL_ID, $shop->getThumbnail()->getId()->getValue())) {
                add_post_meta($postId, self::THUMBNAIL_ID, $shop->getThumbnail()->getId()->getValue());
            }
        }
    }
}
