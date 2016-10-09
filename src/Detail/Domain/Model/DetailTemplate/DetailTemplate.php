<?php
namespace Affilicious\Detail\Domain\Model\DetailTemplate;

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
     * Get the name for the url usage
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
	 * @return Unit
	 */
	public function getUnit()
	{
		return $this->unit;
	}

    /**
     * Set the optional unit like like text, numeric or file.
     *
     * @since 0.6
     * @param Unit $unit
     */
    public function setUnit(Unit $unit)
    {
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
	 * @return HelpText
	 */
	public function getHelpText()
	{
		return $this->helpText;
	}

    /**
     * Set the optional help text
     *
     * @since 0.6
     * @param HelpText $helpText
     */
    public function setHelpText(HelpText $helpText)
    {
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
