<?php
namespace Affilicious\Attribute\Model;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

trait Type_Trait
{
    /**
     * The unique type for display usage.
     *
     * @var Type
     */
    private $type;

    /**
     * Get the type like text or numeric.
     *
     * @since 0.8
     * @return Type
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * Set the type like text or numeric.
     *
     * @since 0.8
     * @param Type $type
     */
    public function set_type(Type $type)
    {
        $this->type = $type;
    }
}
