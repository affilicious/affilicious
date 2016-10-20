<?php
namespace Affilicious\Shop\Infrastructure\Persistence\Wordpress;

use Affilicious\Common\Domain\Exception\InvalidPostTypeException;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Common\Infrastructure\Persistence\Wordpress\AbstractWordpressRepository;
use Affilicious\Shop\Domain\Exception\ShopTemplateDatabaseException;
use Affilicious\Shop\Domain\Exception\ShopTemplateNotFoundException;
use Affilicious\Shop\Domain\Model\ShopTemplate;
use Affilicious\Shop\Domain\Model\ShopTemplateId;
use Affilicious\Shop\Domain\Model\ShopTemplateRepositoryInterface;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class WordpressShopTemplateRepository extends AbstractWordpressRepository implements ShopTemplateRepositoryInterface
{
    /**
     * @inheritdoc
     * @since 0.6
     */
    public function store(ShopTemplate $shopTemplate)
    {
        // Store the shop template into the database
        $defaultArgs = $this->getDefaultArgs($shopTemplate);
        $args = $this->getArgs($shopTemplate, $defaultArgs);
        $id = !empty($args['ID']) ? wp_update_post($args) : wp_insert_post($args);

        // The ID and the name might has changed. Update both values
        if(empty($post)) {
            $post = get_post($id, OBJECT);
            $name = new Name($post->post_name);
            $shopTemplate->setId(new ShopTemplateId($post->ID));
            $shopTemplate->setName($name);
            $shopTemplate->setKey($name->toKey());
        }

        // Store the shop template meta
        $this->storeThumbnail($shopTemplate);

        return $shopTemplate;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function delete(ShopTemplateId $shopTemplateId)
    {
        $shop = $this->findById($shopTemplateId);
        if($shop === null) {
            throw new ShopTemplateNotFoundException($shopTemplateId);
        }

        $post = wp_delete_post($shopTemplateId->getValue(), false);
        if(empty($post)) {
            throw new ShopTemplateDatabaseException($shopTemplateId);
        }

        $shop->setId(null);

        return $shop;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function findById(ShopTemplateId $shopTemplateId)
    {
        $post = get_post($shopTemplateId->getValue());
        if ($post === null || $post->post_status !== 'publish') {
            return null;
        }

        if($post->post_type !== ShopTemplate::POST_TYPE) {
            throw new InvalidPostTypeException($post->post_type, ShopTemplate::POST_TYPE);
        }

        $shop = self::getShopTemplateFromPost($post);
        return $shop;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function findAll()
    {
        $query = new \WP_Query(array(
            'post_type' => ShopTemplate::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        $shops = array();
        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $shop = self::getShopTemplateFromPost($query->post);
                $shops[] = $shop;
            }

            wp_reset_postdata();
        }

        return $shops;
    }

    /**
     * Convert the Wordpress post into a shop template
     *
     * @since 0.6
     * @param \WP_Post $post
     * @return ShopTemplate
     * @throws InvalidPostTypeException
     */
    protected function getShopTemplateFromPost(\WP_Post $post)
    {
        if($post->post_type !== ShopTemplate::POST_TYPE) {
            throw new InvalidPostTypeException($post->post_type, ShopTemplate::POST_TYPE);
        }

        // Title, Name, Key
        $title = new Title($post->post_title);
        $name = new Name($post->post_name);
        $shopTemplate = new ShopTemplate(
            $title,
            $name,
            $name->toKey()
        );

        // ID
        $shopTemplate->setId(new ShopTemplateId($post->ID));

        // Thumbnail
        $shopTemplate = $this->addThumbnail($shopTemplate);

        return $shopTemplate;
    }

    /**
     * Add the thumbnail to the shop template
     *
     * @since 0.6
     * @param ShopTemplate $shopTemplate
     * @return ShopTemplate
     */
    protected function addThumbnail(ShopTemplate $shopTemplate)
    {
        $thumbnailId = get_post_thumbnail_id($shopTemplate->getId()->getValue());
        if (!empty($thumbnailId)) {
            $thumbnail = self::getImageFromAttachmentId($thumbnailId);

            if($thumbnail !== null) {
                $shopTemplate->setThumbnail($thumbnail);
            }
        }

        return $shopTemplate;
    }

    /**
     * Store the thumbnail into the shop template
     *
     * @since 0.6
     * @param ShopTemplate $shopTemplate
     */
    protected function storeThumbnail(ShopTemplate $shopTemplate)
    {
        if ($shopTemplate->hasThumbnail() && !wp_is_post_revision($shopTemplate->getId()->getValue())) {
            $thumbnailId = $shopTemplate->getThumbnail()->getId()->getValue();
            $this->storePostMeta($shopTemplate->getId(), self::THUMBNAIL_ID, $thumbnailId);
        }
    }

    /**
     * Build the default args from the saved shop template in the database
     *
     * @since 0.6
     * @param ShopTemplate $shopTemplate
     *
     * @return array
     */
    protected function getDefaultArgs(ShopTemplate $shopTemplate)
    {
        $defaultArgs = array();
        if($shopTemplate->hasId()) {
            $defaultArgs = get_post($shopTemplate->getId()->getValue(), ARRAY_A);
        }

        return $defaultArgs;
    }

    /**
     * Build the args to save the shop template
     *
     * @since 0.6
     * @param ShopTemplate $shopTemplate
     *
     * @param array $defaultArgs
     * @return array
     */
    protected function getArgs(ShopTemplate $shopTemplate, array $defaultArgs = array())
    {
        $args = wp_parse_args(array(
            'post_title' => $shopTemplate->getTitle()->getValue(),
            'post_status' => 'publish',
            'post_name' => $shopTemplate->getName()->getValue(),
            'post_type' => ShopTemplate::POST_TYPE,
        ), $defaultArgs);

        if($shopTemplate->hasId()) {
            $args['ID'] = $shopTemplate->getId()->getValue();
        }

        return $args;
    }
}
