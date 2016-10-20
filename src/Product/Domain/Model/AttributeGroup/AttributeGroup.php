<?php
namespace Affilicious\Product\Domain\Model\AttributeGroup;

use Affilicious\Common\Domain\Exception\InvalidTypeException;
use Affilicious\Common\Domain\Model\AbstractAggregate;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Attribute\Domain\Model\AttributeTemplateGroupId;
use Affilicious\Product\Domain\Exception\DuplicatedAttributeException;
use Affilicious\Product\Domain\Model\AttributeGroup\Attribute\Attribute;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class AttributeGroup extends AbstractAggregate
{
    /**
     * This ID is the same as the related template
     *
     * @var AttributeTemplateGroupId
     */
    protected $templateId;

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
     * @var Attribute[]
     */
    protected $attributes;

    /**
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @param Key $key
     */
    public function __construct(Title $title, Name $name, Key $key)
    {
        $this->title = $title;
        $this->name = $name;
        $this->key = $key;
        $this->attributes = array();
    }

    /**
     * Check if the attribute group has a template ID
     *
     * @since 0.6
     * @return bool
     */
    public function hasTemplateId()
    {
        return $this->templateId !== null;
    }

    /**
     * Get the attribute group template ID
     *
     * @since 0.6
     * @return null|AttributeTemplateGroupId
     */
    public function getTemplateId()
    {
        return $this->templateId;
    }

    /**
     * Set the attribute group template ID
     *
     * @since 0.6
     * @param null|AttributeTemplateGroupId $templateId
     * @throws InvalidTypeException
     */
    public function setTemplateId($templateId)
    {
        if($templateId !== null && !($templateId instanceof AttributeTemplateGroupId)) {
            throw new InvalidTypeException($templateId, 'Affilicious\Attribute\Domain\Model\AttributeTemplateGroupId');
        }

        $this->templateId = $templateId;
    }

    /**
     * Get the title
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
     * Get the key for database usage
     *
     * @return Key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Check if a attribute with the given name exists
     *
     * @since 0.6
     * @param Name $name
     * @return bool
     */
    public function hasAttribute(Name $name)
    {
        return isset($this->attributes[$name->getValue()]);
    }

    /**
     * Add a new attribute
     *
     * @since 0.6
     * @param Attribute $attribute
     * @throws DuplicatedAttributeException
     */
    public function addAttribute(Attribute $attribute)
    {
        /*
        if($this->hasAttribute($attribute->getName())) {
            throw new DuplicatedAttributeException($attribute, $this);
        }
        */

        $this->attributes[$attribute->getName()->getValue()] = $attribute;
    }

    /**
     * Remove an existing attribute by the name
     *
     * @since 0.6
     * @param Name $name
     */
    public function removeAttribute(Name $name)
    {
        unset($this->attributes[$name->getValue()]);
    }

    /**
     * Get an existing attribute by the name
     * You don't need to check for the name, but you will get null on non-existence
     *
     * @since 0.6
     * @param Name $name
     * @return null|Attribute
     */
    public function getAttribute(Name $name)
    {
        if($this->hasAttribute($name)) {
            return $this->attributes[$name->getValue()];
        }

        return null;
    }

    /**
     * Get all attributes
     *
     * @since 0.6
     * @return Attribute[]
     */
    public function getAttributes()
    {
        $attributes = array_values($this->attributes);

        return $attributes;
    }

    /**
     * Set all attributes
     *
     * @since 0.6
     * @param Attribute[] $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = array();

        // addAttribute checks for the type
        foreach ($attributes as $attribute) {
            $this->addAttribute($attribute);
        }
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function isEqualTo($object)
    {
        return
            $object instanceof self &&
            ($this->hasTemplateId() && $this->getTemplateId()->isEqualTo($object->getTemplateId()) || !$object->hasTemplateId()) &&
            $this->getTitle()->isEqualTo($object->getTitle()) &&
            $this->getName()->isEqualTo($object->getName()) &&
            $this->getKey()->isEqualTo($object->getKey());
            // TODO: A good way to compare two arrays with objects
    }
}
