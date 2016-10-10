<?php
namespace Affilicious\Detail\Domain\Model\DetailTemplate;

use Affilicious\Common\Domain\Exception\InvalidTypeException;
use Affilicious\Common\Domain\Model\AbstractAggregate;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class DetailTemplate extends AbstractAggregate
{
    /**
     * @var Title
     */
    protected $title;

    /**
     * @var Name
     */
    protected $name;

	/**
	 * @var Key
	 */
	protected $key;

	/**
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
     * Get the title for display usage
     *
     * @since 0.6
     * @return Title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the name for url usage
     *
     * @since 0.6
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

	/**
     * Get the key for database usage.
     *
	 * @since 0.6
	 * @return Key
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
     * Get the type like text, numeric or file.
     *
	 * @since 0.6
	 * @return Type
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
     * Check of the detail template has an optional unit.
     *
	 * @since 0.6
	 * @return bool
	 */
	public function hasUnit()
	{
		return $this->unit !== null;
	}

	/**
     * Get the optional unit like text, numeric or file.
     *
	 * @since 0.6
	 * @return null|Unit
	 */
	public function getUnit()
	{
		return $this->unit;
	}

    /**
     * Set the optional unit like like text, numeric or file.
     *
     * @since 0.6
     * @param null|Unit $unit
     * @throws InvalidTypeException
     */
    public function setUnit($unit)
    {
        if($unit !== null && !($unit instanceof Unit)) {
            throw new InvalidTypeException($unit, 'Affilicious\Detail\Domain\Model\DetailTemplate\Unit');
        }

        $this->unit = $unit;
    }

	/**
     * Check of the detail template has an optional help text
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
     * @throws InvalidTypeException
     */
    public function setHelpText($helpText)
    {
        if($helpText !== null && !($helpText instanceof HelpText)) {
            throw new InvalidTypeException($helpText, 'Affilicious\Detail\Domain\Model\DetailTemplate\HelpText');
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
	        $this->getName()->isEqualTo($object->getName()) &&
	        $this->getKey()->isEqualTo($object->getKey()) &&
	        $this->getType()->isEqualTo($object->getType()) &&
			($this->hasUnit() && $this->getUnit()->isEqualTo($object->getUnit()) || !$object->hasUnit()) &&
			($this->hasHelpText() && $this->getHelpText()->isEqualTo($object->getHelpText()) || !$object->hasHelpText());
	}
}
