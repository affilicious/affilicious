<?php
namespace Affilicious\ProductsPlugin\Product\Domain\Model;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

/**
 * Product fields are like templates for the product details
 * In the admin interface, you can build custom fields, which you can fill up
 * with values in the related product category.
 */
class FieldGroup
{
    const POST_TYPE = 'affilicious_product_field_groups';
    const FIELDS = 'affilicious_product_field_groups';

    /**
     * @var \WP_Post
     */
    private $post;

    /**
     * @var Field[]
     */
    private $fields;

    /**
     * @param \WP_Post $post
     */
    public function __construct(\WP_Post $post)
    {
        $this->post = $post;
        $this->fields = array();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->post->ID;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->post->post_title;
    }

    /**
     * Add a new field
     * @param Field $field
     */
    public function addField(Field $field)
    {
        $this->fields[$field->getKey()] = $field;
    }

    /**
     * Remove an existing field by the key
     * @param string $key
     */
    public function removeField($key)
    {
        unset($this->fields[$key]);
    }

    /**
     * Check if a field with the given key exists
     * @param string $key
     * @return bool
     */
    public function hasField($key)
    {
        return isset($this->fields[$key]);
    }

    /**
     * Get an existing field by the key
     * You don't need to check for the key, but you will get null on non-existence
     * @param string $key
     * @return null|Field
     */
    public function getField($key)
    {
        if (!$this->hasField($key)) {
            return null;
        }

        return $this->fields[$key];
    }

    /**
     * Get all fields
     * @return Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Count the number of fields
     * @return int
     */
    public function countFields()
    {
        return count($this->getFields());
    }
}
