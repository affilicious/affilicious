<?php
namespace Affilicious\Common\Application\Setup;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Setup_Interface
{
    /**
     * @since 0.3
     */
    public function init();

    /**
     * @since 0.3
     */
    public function render();
}
