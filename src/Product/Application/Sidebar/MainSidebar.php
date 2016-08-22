<?php
namespace Affilicious\Product\Application\Sidebar;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class MainSidebar implements SidebarInterface
{
    const ID = 'main-sidebar';

    /**
     * @inheritdoc
     * @since 0.3
     */
    public function init()
    {
        register_sidebar(array(
            'name' => __('Main Sidebar', 'affilicious'),
            'id' => self::ID,
            'description' => __('Place your widgets into this sidebar, which is visible on every page.', 'affilicious'),
            'before_title' => '<h4 class="widget-title">',
            'after_title' => '</h4>',
            'before_widget' => '<li class="widget">',
            'after_widget' => '</li>',
        ));
    }
}
