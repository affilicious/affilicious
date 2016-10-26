<?php
namespace Affilicious\Common\Application\Setup;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Setup_Interface
{
    /**
     * Init a new post type
     *
     * @since 0.3
     */
    public function init();

    /**
     * Render a single post type
     *
     * @since 0.3
     */
    public function render();
}
