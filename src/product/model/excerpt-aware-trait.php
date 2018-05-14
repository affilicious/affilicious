<?php
namespace Affilicious\Product\Model;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
trait Excerpt_Aware_Trait
{
    /**
     * The optional product excerpt.
     *
     * @since 0.8
     * @var null|Excerpt
     */
	protected $excerpt;

    /**
     * Check if the product has any excerpt.
     *
     * @since 0.8
     * @return bool
     */
    public function has_excerpt()
    {
        return $this->excerpt !== null;
    }

    /**
     * Set the optional product excerpt.
     *
     * @since 0.8
     * @return null|Excerpt
     */
    public function get_excerpt()
    {
        return $this->excerpt;
    }

    /**
     * Set the optional product excerpt.
     *
     * @since 0.8
     * @param null|Excerpt $excerpt
     */
    public function set_excerpt(Excerpt $excerpt = null)
    {
        $this->excerpt = $excerpt;
    }
}
