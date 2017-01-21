<?php
namespace Affilicious\Common\Model;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Key_Generator_Interface
{
    /**
     * Create a new key from the name.
     *
     * @since 0.8
     * @param Name $name
     * @return Key
     */
    public function generate_from_name(Name $name);

    /**
     * Create a new key from the slug.
     *
     * @since 0.8
     * @param Slug $slug
     * @return Key
     */
    public function generate_from_slug(Slug $slug);
}
