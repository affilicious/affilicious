<?php

/**
 * Overrides the default wp_install_defaults function which creates the dummy content when WordPress is first installed.
 *
 * We simply creates the 'Uncategorised' category. Nothing else.
 *
 * @global wpdb       $wpdb
 *
 * @param int $user_id User ID.
 */
function wp_install_defaults($user_id)
{

    global $wpdb;

    // Default category
    $cat_name = __('Uncategorized');
    /* translators: Default category slug */
    $cat_slug = sanitize_title(_x('Uncategorized', 'Default category slug'));
    $cat_id = 1;

    $wpdb->insert(
        $wpdb->terms,
        array(
            'term_id' => $cat_id,
            'name' => $cat_name,
            'slug' => $cat_slug,
            'term_group' => 0)
    );
    $wpdb->insert(
        $wpdb->term_taxonomy,
        array(
            'term_id' => $cat_id,
            'taxonomy' => 'category',
            'description' => '',
            'parent' => 0,
            'count' => 1
        )
    );
}
