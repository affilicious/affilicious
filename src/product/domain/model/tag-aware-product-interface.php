<?php
namespace Affilicious\Product\Domain\Model;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Tag_Aware_Product_Interface extends Product_Interface
{
    /**
     * Check if the product has any tags.
     *
     * @since 0.7.1
     * @return bool
     */
    public function has_tags();

    /**
     * Get the product tags.
     *
     * @since 0.7.1
     * @return Tag[]
     */
    public function get_tags();

    /**
     * Set the product tags.
     * If you do this, the old images going to be replaced.
     *
     * @since 0.7.1
     * @param Tag[] $tags
     */
    public function set_tags($tags);
}
