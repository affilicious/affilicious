<?php
/**
 * This file contains fallbacks for not existing functions.
 * Imagine a scenario where someone has deactivated a required plugin like carbon fields.
 */
if (!function_exists('carbon_get_post_meta')) {
    function carbon_get_post_meta($id, $name, $type = null)
    {
        return false;
    }
}

if (!function_exists('carbon_get_the_post_meta')) {
    function carbon_get_the_post_meta($name, $type = null)
    {
        return false;
    }
}

if (!function_exists('carbon_get_theme_option')) {
    function carbon_get_theme_option($name, $type = null)
    {
        return false;
    }
}

if (!function_exists('carbon_get_term_meta')) {
    function carbon_get_term_meta($id, $name, $type = null) {
        return false;
    }
}

if (!function_exists('carbon_get_user_meta')) {
    function carbon_get_user_meta($id, $name, $type = null) {
        return false;
    }
}

if (!function_exists('carbon_get_comment_meta')) {
    function carbon_get_comment_meta($id, $name, $type = null) {
        return false;
    }
}
