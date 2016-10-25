<?php
namespace Affilicious\Attribute\Domain\Model\Attribute;

use Affilicious\Common\Domain\Exception\InvalidTypeException;
use Affilicious\Common\Domain\Model\AbstractAggregate;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class AttributeTemplate extends AbstractAggregate
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
	 * @var HelpText
	 */
    protected $helpText;

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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the unique name of the attribute template for url usage
     *
     * @since 0.6
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

	/**
     * Get the key of the attribute template for database usage
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
     * Check of the attribute template has an optional unit.
     *
     * @since 0.6
     * @return bool
     */
    public function hasUnit()
    {
        return $this->unit !== null;
    }

    /**
     * Get the optional unit like text or numeric.
     *
     * @since 0.6
     * @return null|Unit
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set the optional unit like text or numeric.
     *
     * @since 0.6
     * @param null|Unit $unit
     * @throws InvalidTypeException
     */
    public function setUnit($unit)
    {
        if($unit !== null && !($unit instanceof Unit)) {
            throw new InvalidTypeException($unit, 'Affilicious\Attribute\Domain\Model\Unit');
        }

        $this->unit = $unit;
    }

	/**
     * Check if the optional help text exists
     *
	 * @since 0.6
	 * @return bool
	 */
	public function hasHelpText()
	{
		return $this->helpText !== null;
	}

	/**
     * Get the optional help text
     *
	 * @since 0.6
	 * @return null|HelpText
	 */
	public function getHelpText()
	{
		return $this->helpText;
	}

    /**
     * Set the optional help text
     *
     * @since 0.6
     * @param null|HelpText $helpText
     */
    public function setHelpText($helpText)
    {
        if($helpText !== null && !($helpText instanceof HelpText)) {
            throw new InvalidTypeException($helpText, 'Affilicious\Attribute\Domain\Model\HelpText');
        }

        $this->helpText = $helpText;
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
            ($this->hasUnit() && $this->getUnit()->isEqualTo($object->getUnit()) || !$object->hasUnit()) &&
			($this->hasHelpText() && $this->getHelpText()->isEqualTo($object->getHelpText()) || !$object->hasHelpText());
	}
}
