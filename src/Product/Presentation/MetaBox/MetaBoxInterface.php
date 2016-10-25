<?php
namespace Affilicious\Product\Presentation\MetaBox;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

/**
 * @deprecated 1.0
 */
interface MetaBoxInterface
{
    /**
     * Render the html output of the meta box
     *
     * @since 0.3
     * @param \WP_Post $post
     * @param array $args
     */
    public static function render(\WP_Post $post, $args);

    /**
     * Update the meta box data
     *
     * @since 0.3
     * @param int $post_id
     * @param \WP_Post $post
     */
    public static function update($post_id, \WP_Post $post);
}
