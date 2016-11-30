<?php
namespace Affilicious\Common\Domain\Model;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Updateable_Interface
{
    /**
     * Get the date and time of the last update.
     *
     * @since 0.7
     * @return \DateTime
     */
    public function get_updated_at();

    /**
     * Set the date and time of the last update.
     *
     * @since 0.7
     * @param \DateTime $updated_at
     */
    public function set_updated_at(\DateTime $updated_at);
}
