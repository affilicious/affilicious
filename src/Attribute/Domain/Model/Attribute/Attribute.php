<?php
namespace Affilicious\Attribute\Domain\Model\Attribute;

use Affilicious\Common\Domain\Model\AbstractAggregate;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Attribute extends AbstractAggregate
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
     * @var Value
     */
    private $value;

    /**
	 * @var HelpText
	 */
	private $helpText;

    /**
     * @since 0.6
     * @param Key $key
     * @param Name $name
     * @param Type $type
     * @param Value $value
     */
	public function __construct(Key $key, Name $name, Type $type, Value $value)
	{
	    $this->key = $key;
		$this->name = $name;
		$this->type = $type;
        $this->value = $value;
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
     * @return Value
     */
    public function getValue()
    {
        return $this->value;
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
	        $this->getValue()->isEqualTo($object->getValue()) &&
			($this->hasHelpText() && $this->getHelpText()->isEqualTo($object->getHelpText()) || !$object->hasHelpText());
	}
}
