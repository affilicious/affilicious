<?php
namespace Affilicious\Common\Application\Setup;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

interface SetupInterface
{
    /**
     * Init a new post type
     */
    public function init();

    /**
     * Render a single post type
     */
    public function render();
}
