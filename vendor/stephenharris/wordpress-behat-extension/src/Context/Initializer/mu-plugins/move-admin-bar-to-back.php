<?php
/**
 * Selenium can't always target elements on the page because they are 'behind' the
 * admin bar. This is a temporary fix, but it sets the z-index to 0 so the admin
 * bar appears behind the element, making it clickable for Selenium.
 */
add_action('admin_head', function () {
    echo '<style>#wpadminbar {z-index:0!important;}</style>';
});
