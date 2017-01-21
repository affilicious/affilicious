<?php
namespace Affilicious\Common\Model;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

trait Update_Trait
{
    /**
     * The date an time of the last update.
     *
     * @since 0.8
     * @var \DateTimeImmutable
     */
    protected $updated_at;

    /**
     * Get the date and time of the last update.
     *
     * @since 0.8
     * @return \DateTimeImmutable
     */
    public function get_updated_at()
    {
        return $this->updated_at;
    }

    /**
     * Set the date and time of the last update.
     *
     * @since 0.8
     * @param \DateTimeImmutable $updated_at
     */
    public function set_updated_at(\DateTimeImmutable $updated_at)
    {
        $this->updated_at = $updated_at;
    }
}
