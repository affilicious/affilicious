<?php
namespace Affilicious\Product\Application\Sidebar;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

interface SidebarInterface
{
    /**
     * Initialize the sidebar in Wordpress
     *
     * @see https://codex.wordpress.org/Function_Reference/register_sidebar
     * @since 0.3
     *
     */
    public function init();
}
