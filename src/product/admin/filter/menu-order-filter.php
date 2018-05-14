<?php
namespace Affilicious\Product\Admin\Filter;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9
 */
class Menu_Order_Filter
{
    /**
     * @filter custom_menu_order
     * @since 0.9
     * @param $menu_order
     * @return mixed
     */
    function filter($menu_order)
    {
        global $submenu;

        if(!isset($submenu['edit.php?post_type=aff_product'])) {
            return $menu_order;
        }

        $pages = $submenu['edit.php?post_type=aff_product'];

        foreach ($pages as $index => $page) {
            if(isset($page[0]) && $page[0] == __('Import', 'affilicious')) {
                unset($pages[$index]);

                // Insert page after the second one
                $pages = array_slice($pages, 0, 2, true) + [$page] + array_slice($pages, 2, count($pages) - 2, true);

                $submenu['edit.php?post_type=aff_product'] = $pages;

                return $menu_order;
            }
        }

        return $menu_order;
    }
}
