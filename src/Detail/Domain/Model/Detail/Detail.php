<?php
namespace Affilicious\Detail\Domain\Model\Detail;

use Affilicious\Common\Domain\Model\AbstractAggregate;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Detail extends AbstractAggregate
{
	/**
	 * @var Key
	 */
	private $key;

	/**
	 * @var Name
	 */
	private $name;

	/**
	 * @var Type
	 */
	private $type;

	/**
	 * @var Unit
	 */
	private $unit;

	/**
	 * @var HelpText
	 */
	private $helpText;

    /**
     * @since 0.6
     * @param Key $key
     * @param Name $name
     * @param Type $type
     */
	public function __construct(Key $key, Name $name, Type $type)
	{
	    $this->key = $key;
		$this->name = $name;
		$this->type = $type;
	}

	/**
	 * @since 0.6
	 * @return Key
	 */
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * @since 0.6
	 * @return Name
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @since 0.6
	 * @return Type
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @since 0.6
	 * @return bool
	 */
	public function hasUnit()
	{
		return $this->unit !== null;
	}

	/**
	 * @since 0.6
	 * @return Unit
	 */
	public function getUnit()
	{
		return $this->unit;
	}

    /**
     * @since 0.6
     * @param Unit $unit
     */
    public function setUnit(Unit $unit)
    {
        $this->unit = $unit;
    }

	/**
	 * @since 0.6
	 * @return bool
	 */
	public function hasHelpText()
	{
		return $this->helpText !== null;
	}

	/**
	 * @since 0.6
	 * @return HelpText
	 */
	public function getHelpText()
	{
		return $this->helpText;
	}

    /**
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
	        $this->getName()->isEqualTo($object->getName()) &&
	        $this->getType()->isEqualTo($object->getType()) &&
			($this->hasUnit() && $this->getUnit()->isEqualTo($object->getUnit()) || !$object->hasUnit()) &&
			($this->hasHelpText() && $this->getHelpText()->isEqualTo($object->getHelpText()) || !$object->hasHelpText());
	}
}
