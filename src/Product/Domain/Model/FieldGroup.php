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
    const POST_TYPE = 'product_fields';
    const CATEGORY = 'at_product_fields_category';
    const CARBON_CATEGORY_NONE = 'category_none';
    const FIELDS = 'at_product_fields';

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $category;

    /**
     * @var Field[]
     */
    private $fields;

    /**
     * @param int $id
     * @param string $title
     * @param string $category
     */
    public function __construct($id, $title, $category)
    {
        $this->id = $id;
        $this->title = $title;
        $this->category = $category;
        $this->fields = array();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
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
