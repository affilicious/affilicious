<?php
namespace Affilicious\Product\Model;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
interface Excerpt_Aware_Interface
{
    /**
     * Check if the product has any excerpt.
     *
     * @since 0.8
     * @return bool
     */
    public function has_excerpt();

    /**
     * Set the optional product excerpt.
     *
     * @since 0.8
     * @return null|Excerpt
     */
    public function get_excerpt();

    /**
     * Set the optional product excerpt.
     *
     * @since 0.8
     * @param null|Excerpt $excerpt
     */
    public function set_excerpt(Excerpt $excerpt = null);
}
