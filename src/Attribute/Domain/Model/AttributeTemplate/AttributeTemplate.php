<?php
namespace Affilicious\Attribute\Domain\Model\AttributeTemplate;

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
     * Holds the type like text, numeric or file
     *
	 * @var Type
	 */
    protected $type;

    /**
     * @var Value
     */
    protected $value;

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
     * Get the type like text, numeric or file
     *
	 * @since 0.6
	 * @return Type
	 */
	public function getType()
	{
		return $this->type;
	}

    /**
     * Get the concrete value
     *
     * @return Value
     */
    public function getValue()
    {
        return $this->value;
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
	        $this->getValue()->isEqualTo($object->getValue()) &&
			($this->hasHelpText() && $this->getHelpText()->isEqualTo($object->getHelpText()) || !$object->hasHelpText());
	}
}
