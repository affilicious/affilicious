<?php
namespace Affilicious\Product\Model;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

trait Content_Aware_Trait
{
    /**
     * The optional product content.
     *
     * @var null|Content
     */
	protected $content;

    /**
     * Check if the product has any content.
     *
     * @since 0.8
     * @return bool
     */
    public function has_content()
    {
        return $this->content !== null;
    }

    /**
     * Set the optional product content.
     *
     * @since 0.8
     * @return null|Content
     */
    public function get_content()
    {
        return $this->content;
    }

    /**
     * Set the optional product content.
     *
     * @since 0.8
     * @param null|Content $content
     */
    public function set_content(Content $content = null)
    {
        $this->content = $content;
    }
}
