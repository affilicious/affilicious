<?php
namespace Affilicious\Product\Domain\Model\AttributeGroup\Attribute;

use Affilicious\Common\Domain\Exception\InvalidTypeException;
use Affilicious\Common\Domain\Model\AbstractAggregate;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Attribute extends AbstractAggregate
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the unique name of the attribute for url usage
     *
     * @since 0.6
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

	/**
     * Get the key of the attribute for database usage
     *
	 * @since 0.6
	 * @return Key
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
     * Get the type like text or numeric
     *
	 * @since 0.6
	 * @return Type
	 */
	public function getType()
	{
		return $this->type;
	}

    /**
     * Get the concrete value of the attribute
     *
     * @since 0.6
     * @return Value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Check of the attribute has an optional unit.
     *
     * @since 0.6
     * @return bool
     */
    public function hasUnit()
    {
        return $this->unit !== null;
    }

    /**
     * Get the optional unit like kg, cm or m²
     *
     * @since 0.6
     * @return null|Unit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set the optional unit like kg, cm or m².
     *
     * @since 0.6
     * @param null|Unit $unit
     * @throws InvalidTypeException
     */
    public function setUnit($unit)
    {
        if($unit !== null && !($unit instanceof Unit)) {
            throw new InvalidTypeException($unit, 'Affilicious\Product\Domain\Model\AttributeGroup\Attribute\Unit');
        }

        $this->unit = $unit;
    }

	/**
	 * @inheritdoc
	 * @since 0.6
	 */
	public function isEqualTo($object)
	{
		return
			$object instanceof self &&
	        $this->getTitle()->isEqualTo($object->getTitle()) &&
	        $this->getType()->isEqualTo($object->getType()) &&
	        $this->getValue()->isEqualTo($object->getValue()) &&
            ($this->hasUnit() && $this->getUnit()->isEqualTo($object->getUnit()) || !$object->hasUnit());
	}
}
