<?php
namespace Affilicious\Attribute\Domain\Model;

use Affilicious\Attribute\Domain\Model\Attribute\Attribute;
use Affilicious\Attribute\Domain\Model\Attribute\Key as AttributeKey;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class AttributeGroup
{
    const POST_TYPE = 'attribute_group';

	/**
	 * @var AttributeGroupId
	 */
	private $id;

    /**
     * @var Key
     */
    private $key;

	/**
	 * @var Title
	 */
	private $title;

    /**
     * @var Attribute[]
     */
    private $attributes;

    /**
     * @since 0.6
     * @param AttributeGroupId $id
     * @param Key $key
     * @param Title $title
     */
    public function __construct(AttributeGroupId $id, Key $key, Title $title)
    {
	    $this->id = $id;
        $this->key = $key;
        $this->title = $title;
        $this->attributes = array();
    }

    /**
     * @since 0.6
     * @return AttributeGroupId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @since 0.6
     * @return Title
     */
    public function getTitle()
    {
        return $this->title;
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
     * @param AttributeKey $key
     */
    public function removeAttribute(AttributeKey $key)
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
     * @param AttributeKey $key
     * @return bool
     */
    public function hasAttribute(AttributeKey $key)
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
     * @param AttributeKey $key
     * @return null|Attribute
     */
    public function getAttribute(AttributeKey $key)
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
        return get_post($this->id->getValue());
    }
}
