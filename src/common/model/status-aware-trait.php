<?php
namespace Affilicious\Common\Model;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9
 */
trait Status_Aware_Trait
{
    /**
     * The post status.
     *
     * @since 0.9
     * @var Status
     */
    protected $status;

    /**
     * Set the post status.
     *
     * @since 0.9
     * @param Status $status
     */
    public function set_status(Status $status)
    {
        $this->status = $status;
    }

    /**
     * Get the post status.
     *
     * @since 0.9
     * @return Status
     */
    public function get_status()
    {
        return $this->status;
    }
}
