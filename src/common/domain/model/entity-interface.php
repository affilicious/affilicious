<?php
namespace Affilicious\Common\Domain\Model;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Entity_Interface
{
    /**
     * Check if the entity has an optional ID.
     *
     * @since 0.7
     * @return bool
     */
    public function has_id();

    /**
     * Get the optional ID of the entity.
     *
     * @since 0.6
     * @return null|Value_Object_Interface
     */
    public function get_id();

    /**
     * Set the optional ID of the entity.
     *
     * @since 0.7
     * @param null|Value_Object_Interface $id
     * @return Value_Object_Interface
     */
    public function set_id($id);

    /**
     * Check if the properties of the other object are equal to the entity properties.
     *
     * @since 0.6
     * @param mixed $object
     * @return bool
     */
    public function is_equal_to($object);
}
