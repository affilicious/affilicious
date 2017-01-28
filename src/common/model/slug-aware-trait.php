<?php
namespace Affilicious\Common\Model;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

trait Slug_Aware_Trait
{
    /**
     * The unique slug for url usage.
     *
     * @var Slug
     */
    private $slug;

    /**
     * Set the unique slug for url usage.
     *
     * @since 0.8
     * @param Slug $slug
     */
    public function set_slug(Slug $slug)
    {
        $this->slug = $slug;
    }

    /**
     * Get the unique slug for url usage.
     *
     * @since 0.8
     * @return Slug
     */
    public function get_slug()
    {
        return $this->slug;
    }
}
