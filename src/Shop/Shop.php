<?php
namespace Affilicious\ProductsPlugin\Shop;

if (!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class Shop
{
    const POST_TYPE = 'shop';

    /**
     * @var \WP_Post
     */
    private $post;

    /**
     * @param \WP_Post $post
     */
    public function __construct(\WP_Post $post)
    {
        $this->post = $post;
    }

    /**
     * Get the shop ID
     * @return int
     */
    public function getId()
    {
        return $this->post->ID;
    }

    /**
     * Get the shop title
     * @return string
     */
    public function getTitle()
    {
        return $this->post->post_title;
    }

    /**
     * Get the shop content
     * @return string
     */
    public function getContent()
    {
        return $this->post->post_content;
    }

    /**
     * Check if the shop has a logo
     * @return bool
     */
    public function hasLogo()
    {
        $shopLogoId = get_post_thumbnail_id($this->getId());
        return $shopLogoId == false ? false : true;
    }

    /**
     * Get the shop logo
     * @return null|string
     */
    public function getLogo()
    {
        $shopLogoId = get_post_thumbnail_id($this->getId());
        if (!$shopLogoId) {
            return null;
        }

        $shopLogo = wp_get_attachment_image_src($shopLogoId, 'featured_preview');
        return $shopLogo[0];
    }

    /**
     * Get the raw Wordpress post
     * @return \WP_Post
     */
    public function getRawPost()
    {
        return $this->post;
    }
}
