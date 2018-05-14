<?php
namespace Affilicious\Common\Model;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.7
 */
interface Update_Aware_Interface
{
    /**
     * Get the date and time of the last update.
     *
     * @since 0.7
     * @return \DateTimeImmutable
     */
    public function get_updated_at();

    /**
     * Set the date and time of the last update.
     *
     * @since 0.7
     * @param \DateTimeImmutable $updated_at
     */
    public function set_updated_at(\DateTimeImmutable $updated_at);
}
