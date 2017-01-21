<?php
namespace Affilicious\Common\Model;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

trait Name_Trait
{
    /**
     * The unique name for display usage.
     *
     * @var Name
     */
    private $name;

    /**
     * Set the unique name for display usage.
     *
     * @since 0.8
     * @param Name $name
     */
    public function set_name(Name $name)
    {
        $this->name = $name;
    }

    /**
     * Get the unique name for display usage.
     *
     * @since 0.8
     * @return Name
     */
    public function get_name()
    {
        return $this->name;
    }
}
