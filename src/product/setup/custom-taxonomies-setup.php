<?php
namespace Affilicious\Product\Setup;

use Affilicious\Product\Model\Product;

class Custom_Taxonomies_Setup
{
    /**
     * @hook init
     * @since 0.9
     */
    public function init()
    {
        do_action('aff_custom_taxonomies_before_init');

        $taxonomies = carbon_get_theme_option('affilicious_options_product_container_taxonomies_tab_taxonomies_field', 'complex');
        if(!empty($taxonomies)) {
            foreach ($taxonomies as $taxonomy) {
                $labels = $this->get_labels($taxonomy);

                if(!empty($labels)) {
                    $args = array(
                        'hierarchical'      => true,
                        'labels'            => $labels,
                        'show_ui'           => true,
                        'show_admin_column' => true,
                        'show_in_nav_menus' => true,
                        'query_var'         => true,
                        'rewrite'           => array('slug' => $taxonomy['slug']),
                        'public'            => true,
                    );

                    $args = apply_filters('aff_custom_taxonomies_init_args', $args, $taxonomy['taxonomy']);
                    register_taxonomy($taxonomy['taxonomy'], Product::POST_TYPE, $args);
                }
            }
        }

        do_action('aff_custom_taxonomies_after_init');
    }

    /**
     * Get the labels for the custom taxonomy.
     *
     * @since 0.9
     * @param array $taxonomy
     * @return array|null
     */
    private function get_labels($taxonomy)
    {
        if(empty($taxonomy['singular_name']) || empty($taxonomy['plural_name'])) {
            return null;
        }

        return array(
            'name'              => sprintf(__('%s', 'affilicious'), $taxonomy['plural_name']),
            'singular_name'     => sprintf(__('%s', 'affilicious'), $taxonomy['singular_name']),
            'search_items'      => sprintf(__('Search %s', 'affilicious'), $taxonomy['plural_name']),
            'all_items'         => sprintf(__('All %s', 'affilicious'), $taxonomy['plural_name']),
            'parent_item'       => sprintf(__('Parent %s', 'affilicious'), $taxonomy['singular_name']),
            'parent_item_colon' => sprintf(__('Parent %s:', 'affilicious'), $taxonomy['singular_name']),
            'edit_item'         => sprintf(__('Edit %s', 'affilicious'), $taxonomy['singular_name']),
            'update_item'       => sprintf(__('Update %s', 'affilicious'), $taxonomy['singular_name']),
            'add_new_item'      => sprintf(__('Add New %s', 'affilicious'), $taxonomy['singular_name']),
            'new_item_name'     => sprintf(__('New %s', 'affilicious'), $taxonomy['singular_name']),
            'menu_name'         => sprintf(__('%s', 'affilicious'), $taxonomy['plural_name']),
        );
    }
}
