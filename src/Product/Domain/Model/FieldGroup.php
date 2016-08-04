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
    const POST_TYPE = 'field_group';

    const FIELD_ID = 'field_group_id';
    const FIELD_KEY = 'key';
    const FIELD_TYPE = 'type';
    const FIELD_LABEL = 'label';
    const FIELD_VALUE = 'value';
    const FIELD_DEFAULT_VALUE = 'default_value';
    const FIELD_HELP_TEXT = 'help_text';

    const FIELD_TYPE_TEXT = 'text';
    const FIELD_TYPE_NUMBER = 'number';
    const FIELD_TYPE_FILE = 'file';

    /**
     * @var \WP_Post
     */
    private $post;

    /**
     * @var array
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
     * @return string
     */
    public function getName()
    {
        return $this->post->post_name;
    }

    /**
     * Add a new field
     * @param array $field
     */
    public function addField(array $field)
    {
        $this->fields[] = $field;
    }

    /**
     * Remove an existing field by the key
     * @param string $key
     */
    public function removeField($key)
    {
        foreach ($this->fields as $position => $field) {
            if (isset($field[self::FIELD_KEY]) && $field[self::FIELD_KEY] === $key) {
                unset($this->fields[$position]);
                break;
            }
        }
    }

    /**
     * Check if a field with the given key exists
     * @param string $key
     * @return bool
     */
    public function hasField($key)
    {
        foreach ($this->fields as $field) {
            if (isset($field[self::FIELD_KEY]) && $field[self::FIELD_KEY] === $key) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get an existing field by the key
     * You don't need to check for the key, but you will get null on non-existence
     * @param string $key
     * @return null|array
     */
    public function getField($key)
    {
        foreach ($this->fields as $position => $field) {
            if (isset($field[self::FIELD_KEY]) && $field[self::FIELD_KEY] === $key) {
                return $field;
            }
        }

        return null;
    }

    /**
     * Get all fields
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Set the fields
     * @param array $fields
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }
}
