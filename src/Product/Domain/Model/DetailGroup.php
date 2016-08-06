<?php
namespace Affilicious\ProductsPlugin\Product\Domain\Model;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

/**
 * Product fields are like templates for the product details
 * In the admin interface, you can build custom fields, which you can fill up
 * with values in the related product category.
 */
class DetailGroup
{
    const POST_TYPE = 'detail_group';

    const DETAIL_ID = 'detail_group_id';
    const DETAIL_KEY = 'key';
    const DETAIL_TYPE = 'type';
    const DETAIL_LABEL = 'label';
    const DETAIL_VALUE = 'value';
    const DETAIL_DEFAULT_VALUE = 'default_value';
    const DETAIL_HELP_TEXT = 'help_text';

    const DETAIL_TYPE_TEXT = 'text';
    const DETAIL_TYPE_NUMBER = 'number';
    const DETAIL_TYPE_FILE = 'file';

    /**
     * @var \WP_Post
     */
    private $post;

    /**
     * @var array
     */
    private $details;

    /**
     * @param \WP_Post $post
     */
    public function __construct(\WP_Post $post)
    {
        $this->post = $post;
        $this->details = array();
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
     * Add a new detail
     * @param array $detail
     */
    public function addDetail(array $detail)
    {
        $this->details[] = $detail;
    }

    /**
     * Remove an existing detail by the key
     * @param string $key
     */
    public function removeDetail($key)
    {
        foreach ($this->details as $position => $detail) {
            if (isset($detail[self::FIELD_KEY]) && $detail[self::FIELD_KEY] === $key) {
                unset($this->details[$position]);
                break;
            }
        }
    }

    /**
     * Check if a detail with the given key exists
     * @param string $key
     * @return bool
     */
    public function hasDetail($key)
    {
        foreach ($this->details as $detail) {
            if (isset($detail[self::FIELD_KEY]) && $detail[self::FIELD_KEY] === $key) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get an existing detail by the key
     * You don't need to check for the key, but you will get null on non-existence
     * @param string $key
     * @return null|array
     */
    public function getDetail($key)
    {
        foreach ($this->details as $position => $detail) {
            if (isset($detail[self::FIELD_KEY]) && $detail[self::FIELD_KEY] === $key) {
                return $detail;
            }
        }

        return null;
    }

    /**
     * Get all details
     * @return array
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set the details
     * @param array $details
     */
    public function setDetails(array $details)
    {
        $this->details = $details;
    }
}
