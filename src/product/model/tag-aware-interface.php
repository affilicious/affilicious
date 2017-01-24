<?php
namespace Affilicious\Product\Model;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Tag_Aware_Interface
{
    /**
     * Check if the product has any tags.
     *
     * @since 0.8
     * @return bool
     */
    public function has_tags();

    /**
     * Get the product tags.
     *
     * @since 0.8
     * @return Tag[]
     */
    public function get_tags();

    /**
     * Set the product tags.
     * If you do this, the old tags going to be replaced.
     *
     * @since 0.8
     * @param Tag[] $tags
     */
    public function set_tags($tags);
}
