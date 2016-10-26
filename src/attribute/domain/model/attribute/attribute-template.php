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

class Attribute_Template extends Abstract_Aggregate
{
    /**
     * The title of the attribute template for display usage
     *
     * @var Title
     */
    protected $title;

    /**
     * The unique name of the attribute template for url usage
     *
     * @var Name
     */
    protected $name;

    /**
     * The key of the attribute template for database usage
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
     * @var Unit
     */
    protected $unit;

    /**
	 * @var Help_Text
	 */
    protected $help_text;

    /**
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @param Key $key
     * @param Type $type
     */
	public function __construct(Title $title, Name $name, Key $key, Type $type)
	{
        $this->title = $title;
        $this->name = $name;
        $this->key = $key;
		$this->type = $type;
    }

    /**
     * Get the title of the attribute template for display usage
     *
     * @since 0.6
     * @return Title
     */
    public function get_title()
    {
        return $this->title;
    }

    /**
     * Get the unique name of the attribute template for url usage
     *
     * @since 0.6
     * @return Name
     */
    public function get_name()
    {
        return $this->name;
    }

	/**
     * Get the key of the attribute template for database usage
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
     * Check of the attribute template has an optional unit.
     *
     * @since 0.6
     * @return bool
     */
    public function has_unit()
    {
        return $this->unit !== null;
    }

    /**
     * Get the optional unit like text or numeric.
     *
     * @since 0.6
     * @return null|Unit
     */
    public function get_unit()
    {
        return $this->unit;
    }

    /**
     * Set the optional unit like text or numeric.
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
     * Check if the optional help text exists
     *
	 * @since 0.6
	 * @return bool
	 */
	public function has_help_text()
	{
		return $this->help_text !== null;
	}

	/**
     * Get the optional help text
     *
	 * @since 0.6
	 * @return null|Help_Text
	 */
	public function get_help_text()
	{
		return $this->help_text;
	}

    /**
     * Set the optional help text
     *
     * @since 0.6
     * @param null|Help_Text $help_text
     */
    public function set_help_text($help_text)
    {
        if($help_text !== null && !($help_text instanceof Help_Text)) {
            throw new Invalid_Type_Exception($help_text, 'Affilicious\Attribute\Domain\Model\Help_Text');
        }

        $this->help_text = $help_text;
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
            ($this->has_unit() && $this->get_unit()->is_equal_to($object->get_unit()) || !$object->has_unit()) &&
			($this->has_help_text() && $this->get_help_text()->is_equal_to($object->get_help_text()) || !$object->has_help_text());
	}
}
