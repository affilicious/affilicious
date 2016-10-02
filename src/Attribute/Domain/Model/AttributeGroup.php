<?php
namespace Affilicious\Attribute\Domain\Model;

use Affilicious\Attribute\Domain\Model\Attribute\Attribute;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class AttributeGroup
{
    const POST_TYPE = 'attribute_group';

	/**
	 * @var AttributeGroupId
	 */
	protected $id;

    /**
     * @var Title
     */
    protected $title;

    /**
     * @var Title
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
     * Check if the attribute group has an ID
     *
     * @since 0.6
     * @return bool
     */
    public function hasId()
    {
        return $this->id !== null;
    }

    /**
     * Get the attribute group ID
     *
     * @since 0.6
     * @return AttributeGroupId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the attribute group ID
     *
     * Note that you just get the ID in Wordpress, if you store a post.
     * Normally, you place the ID to the constructor, but it's not possible here
     *
     * @since 0.6
     * @param null|AttributeGroupId $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * Set the title
     *
     * @since 0.6
     * @param Title $title
     */
    public function setTitle(Title $title)
    {
        $this->title = $title;
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
     * Set the name for the url usage
     *
     * @since 0.6
     * @param Name $name
     */
    public function setName(Name $name)
    {
        $this->name = $name;
    }

	/**
	 * @return Key
	 */
	public function getKey()
	{
		return $this->key;
	}

    /**
     * Add a new attribute
     *
     * @since 0.6
     * @param Attribute $attribute
     */
    public function addAttribute(Attribute $attribute)
    {
        $this->attributes[] = $attribute;
    }

    /**
     * Remove an existing attribute by the key
     *
     * @since 0.6
     * @param Key $key
     */
    public function removeAttribute(Key $key)
    {
        foreach ($this->attributes as $index => $attribute) {
        	if($attribute->getKey()->isEqualTo($key)) {
		        unset($this->attributes[$index]);
		        break;
	        }
        }
    }

    /**
     * Check if a attribute with the given key exists
     *
     * @since 0.6
     * @param Key $key
     * @return bool
     */
    public function hasAttribute(Key $key)
    {
        foreach ($this->attributes as $attribute) {
	        if($attribute->getKey()->isEqualTo($key)) {
		        return true;
	        }
        }

        return false;
    }

    /**
     * Get an existing attribute by the key
     * You don't need to check for the key, but you will get null on non-existence
     *
     * @since 0.6
     * @param Key $key
     * @return null|Attribute
     */
    public function getAttribute(Key $key)
    {
        foreach ($this->attributes as $attribute) {
	        if($attribute->getKey()->isEqualTo($key)) {
		        return $attribute;
	        }
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
        return $this->attributes;
    }

    /**
     * Set all attributes
     *
     * @since 0.6
     * @param Attribute[] $attributes
     */
    public function setAttributes($attributes)
    {
    	// addAttribute checks for the type
    	foreach ($attributes as $attribute) {
    		$this->addAttribute($attribute);
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
}
