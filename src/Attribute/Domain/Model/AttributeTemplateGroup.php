<?php
namespace Affilicious\Attribute\Domain\Model;

use Affilicious\Attribute\Domain\Exception\DuplicatedAttributeTemplateException;
use Affilicious\Attribute\Domain\Model\AttributeTemplate\AttributeTemplate;
use Affilicious\Common\Domain\Exception\InvalidTypeException;
use Affilicious\Common\Domain\Model\AbstractEntity;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class AttributeTemplateGroup extends AbstractEntity
{
    /**
     * There is a limit of 20 characters for post types in Wordpress
     */
    const POST_TYPE = 'aff_attr_template';

	/**
     * The unique ID of the attribute template group
     * Note that you just get the ID in Wordpress, if you store a post.
     *
	 * @var AttributeTemplateGroupId
	 */
	protected $id;

    /**
     * The title of the attribute template group for display usage
     *
     * @var Title
     */
    protected $title;

    /**
     * The unique name of the attribute template group for url usage
     *
     * @var Name
     */
    protected $name;

    /**
     * The key of the attribute template group for database usage
     *
     * @var Key
     */
    protected $key;

    /**
     * Holds all attributes templates to build the concrete attributes
     *
     * @var AttributeTemplate[]
     */
    protected $attributeTemplates;

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
        $this->attributeTemplates = array();
    }

    /**
     * Check if the attribute template group has an ID
     *
     * @since 0.6
     * @return bool
     */
    public function hasId()
    {
        return $this->id !== null;
    }

    /**
     * Get the optional attribute template group ID
     *
     * @since 0.6
     * @return null|AttributeTemplateGroupId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the optional attribute template group ID
     *
     * Note that you just get the ID in Wordpress, if you store a post.
     * Normally, you place the ID to the constructor, but it's not possible here
     *
     * @since 0.6
     * @param null|AttributeTemplateGroupId $id
     */
    public function setId($id)
    {
        if($id !== null && !($id instanceof AttributeTemplateGroupId)) {
            throw new InvalidTypeException($id, 'Affilicious\Attribute\Domain\Model\AttributeTemplateGroupId');
        }

        $this->id = $id;
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
     * Get the unique name for url usage
     *
     * @since 0.6
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the unique name for the url usage
     *
     * @since 0.6
     * @param Name $name
     */
    public function setName(Name $name)
    {
        $this->name = $name;
    }

	/**
     * Get the key for the database usage
     *
     * @since 0.6
	 * @return Key
	 */
	public function getKey()
	{
		return $this->key;
	}

    /**
     * Check if a attribute template with the given name exists
     *
     * @since 0.6
     * @param Name $name
     * @return bool
     */
    public function hasAttributeTemplate(Name $name)
    {
        return isset($this->attributeTemplates[$name->getValue()]);
    }

    /**
     * Add a new attribute template
     *
     * @since 0.6
     * @param AttributeTemplate $attributeTemplate
     */
    public function addAttributeTemplate(AttributeTemplate $attributeTemplate)
    {
        if($this->hasAttributeTemplate($attributeTemplate->getName())) {
            throw new DuplicatedAttributeTemplateException($attributeTemplate, $this);
        }

        $this->attributeTemplates[$attributeTemplate->getName()->getValue()] = $attributeTemplate;
    }

    /**
     * Remove an existing attribute template by the name
     *
     * @since 0.6
     * @param Name $name
     */
    public function removeAttributeTemplate(Name $name)
    {
        unset($this->attributeTemplates[$name->getValue()]);
    }

    /**
     * Get an existing attribute template by the name
     * You don't need to check for the name, but you will get null on non-existence
     *
     * @since 0.6
     * @param Name $name
     * @return null|AttributeTemplate
     */
    public function getAttribute(Name $name)
    {
        if($this->hasAttributeTemplate($name)) {
            return $this->attributeTemplates[$name->getValue()];
        }

        return null;
    }

    /**
     * Get all attribute templates
     *
     * @since 0.6
     * @return AttributeTemplate[]
     */
    public function getAttributeTemplates()
    {
        $attributeTemplates = array_values($this->attributeTemplates);

        return $attributeTemplates;
    }

    /**
     * Set all attribute templates
     * If you do this, the old templates going to be replaced.
     *
     * @since 0.6
     * @param AttributeTemplate[] $attributeTemplates
     */
    public function setAttributeTemplates($attributeTemplates)
    {
        $this->attributeTemplates = array();

    	// addAttributeTemplate checks for the type
    	foreach ($attributeTemplates as $attribute) {
    		$this->addAttributeTemplate($attribute);
	    }
    }

    /**
     * Get the raw post
     *
     * @since 0.6
     * @return null|\WP_Post
     */
    public function getRawPost()
    {
        if(!$this->hasId()) {
            return null;
        }

        return get_post($this->id->getValue());
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function isEqualTo($object)
    {
        return
            $object instanceof self &&
            ($this->hasId() && $this->getId()->isEqualTo($object->getId()) || !$object->hasId()) &&
            $this->getTitle()->isEqualTo($object->getTitle()) &&
            $this->getName()->isEqualTo($object->getName());
            // TODO: Compare the rest and check the best way to compare two arrays with objects inside
    }
}
