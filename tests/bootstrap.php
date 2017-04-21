<?php

// Give access to tests_add_filter() function.
require __DIR__ . '/../tmp/wordpress-tests-lib/includes/functions.php';

// Manually load the plugin being tested.
tests_add_filter('muplugins_loaded', function() {
    require dirname(dirname(__FILE__)) . '/affilicious.php';
});

// Start up the WP testing environment.
require __DIR__ . '/../tmp/wordpress-tests-lib/includes/bootstrap.php';
