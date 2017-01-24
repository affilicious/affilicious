<?php
namespace Affilicious\Product\Model;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Content_Aware_Interface
{
    /**
     * Check if the product has any content.
     *
     * @since 0.8
     * @return bool
     */
    public function has_content();

    /**
     * Set the optional product content.
     *
     * @since 0.8
     * @return null|Content
     */
    public function get_content();

    /**
     * Set the optional product content.
     *
     * @since 0.8
     * @param null|Content $content
     */
    public function set_content(Content $content = null);
}
