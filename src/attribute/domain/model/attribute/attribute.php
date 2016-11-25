<?php
namespace Affilicious\Attribute\Domain\Model\Attribute;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Abstract_Aggregate;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Attribute extends Abstract_Aggregate
{
    /**
     * The title of the attribute for display usage
     *
     * @var Title
     */
    protected $title;

    /**
     * The unique name of the attribute for url usage
     *
     * @var Name
     */
    protected $name;

    /**
     * The key of the attribute for database usage
     *
	 * @var Key
	 */
    protected $key;

	/**
     * Holds the type like text or numeric
     *
	 * @var Type
	 */
    protected $type;

    /**
     * Holds the optional unit like kg, cm or m²
     *
     * @var Unit
     */
    protected $unit;

    /**
     * Holds the concrete value
     *
     * @var Value
     */
    protected $value;

    /**
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @param Key $key
     * @param Type $type
     * @param Value $value
     */
	public function __construct(Title $title, Name $name, Key $key, Type $type, Value $value)
	{
        $this->title = $title;
        $this->name = $name;
        $this->key = $key;
		$this->type = $type;
        $this->value = $value;
    }

    /**
     * Get the title of the attribute for display usage
     *
     * @since 0.6
     * @return Title
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * Get the unique name of the attribute for url usage
     *
     * @since 0.6
     * @return Name
     */
    public function get_name()
    {
        return $this->name;
    }

	/**
     * Get the key of the attribute for database usage
     *
	 * @since 0.6
	 * @return Key
	 */
	public function get_key()
	{
		return $this->key;
	}

	/**
     * Get the type like text or numeric
     *
	 * @since 0.6
	 * @return Type
	 */
	public function get_type()
	{
		return $this->type;
	}

    /**
     * Get the concrete value of the attribute
     *
     * @since 0.6
     * @return Value
     */
    public function get_value()
    {
        return $this->value;
    }

    /**
     * Check of the attribute has an optional unit.
     *
     * @since 0.6
     * @return bool
     */
    public function has_unit()
    {
        return $this->unit !== null;
    }

    /**
     * Get the optional unit like kg, cm or m²
     *
     * @since 0.6
     * @return null|Unit
     */
    public function get_unit()
    {
        return $this->unit;
    }

    /**
     * Set the optional unit like kg, cm or m².
     *
     * @since 0.6
     * @param null|Unit $unit
     * @throws Invalid_Type_Exception
     */
    public function set_unit($unit)
    {
        if($unit !== null && !($unit instanceof Unit)) {
            throw new Invalid_Type_Exception($unit, 'Affilicious\Attribute\Domain\Model\Unit');
        }

        $this->unit = $unit;
    }

	/**
	 * @inheritdoc
	 * @since 0.6
	 */
	public function is_equal_to($object)
	{
		return
			$object instanceof self &&
	        $this->get_title()->is_equal_to($object->get_title()) &&
	        $this->get_type()->is_equal_to($object->get_type()) &&
	        $this->get_value()->is_equal_to($object->get_value()) &&
            ($this->has_unit() && $this->get_unit()->is_equal_to($object->get_unit()) || !$object->has_unit());
	}
}
