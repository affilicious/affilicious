<?php
namespace Affilicious\Attribute\Model;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

trait Unit_Trait
{
    /**
     * The optional unit like kg, cm or m².
     *
     * @var null|Unit
     */
	protected $unit;

    /**
     * Check if the optional unit exists.
     *
     * @since 0.8
     * @return bool
     */
    public function has_unit()
    {
        return $this->unit !== null;
    }

    /**
     * Get the optional unit like kg, cm or m².
     *
     * @since 0.8
     * @return null|Unit
     */
    public function get_unit()
    {
        return $this->unit;
    }

    /**
     * Set the optional unit like kg, cm or m².
     *
     * @since 0.8
     * @param null|Unit $unit
     */
    public function set_unit(Unit $unit = null)
    {
        $this->unit = $unit;
    }
}
