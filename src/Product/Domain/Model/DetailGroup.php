<?php
namespace Affilicious\Product\Domain\Model;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class DetailGroup
{
    const POST_TYPE = 'detail_group';

    const DETAIL_ID = 'detail_group_id';
    const DETAIL_KEY = 'key';
    const DETAIL_TYPE = 'type';
    const DETAIL_NAME = 'name';
    const DETAIL_VALUE = 'value';
    const DETAIL_UNIT = 'unit';
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
     * @since 0.3
     * @param \WP_Post $post
     */
    public function __construct(\WP_Post $post)
    {
        $this->post = $post;
        $this->details = array();
    }

    /**
     * @since 0.3
     * @return int
     */
    public function getId()
    {
        return $this->post->ID;
    }

    /**
     * @since 0.3
     * @return string
     */
    public function getTitle()
    {
        return $this->post->post_title;
    }

    /**
     * @since 0.3
     * @return string
     */
    public function getName()
    {
        return $this->post->post_name;
    }

    /**
     * Add a new detail
     *
     * @since 0.3
     * @param array $detail
     */
    public function addDetail($detail)
    {
        $this->details[] = $detail;
    }

    /**
     * Remove an existing detail by the key
     *
     * @since 0.3
     * @param string $key
     */
    public function removeDetail($key)
    {
        foreach ($this->details as $position => $detail) {
            if (isset($detail[self::DETAIL_KEY]) && $detail[self::DETAIL_KEY] === $key) {
                unset($this->details[$position]);
                break;
            }
        }
    }

    /**
     * Check if a detail with the given key exists
     *
     * @since 0.3
     * @param string $key
     * @return bool
     */
    public function hasDetail($key)
    {
        foreach ($this->details as $detail) {
            if (isset($detail[self::DETAIL_KEY]) && $detail[self::DETAIL_KEY] === $key) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get an existing detail by the key
     * You don't need to check for the key, but you will get null on non-existence
     *
     * @since 0.3
     * @param string $key
     * @return null|array
     */
    public function getDetail($key)
    {
        foreach ($this->details as $position => $detail) {
            if (isset($detail[self::DETAIL_KEY]) && $detail[self::DETAIL_KEY] === $key) {
                return $detail;
            }
        }

        return null;
    }

    /**
     * Get all details
     *
     * @since 0.3
     * @return array
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set the details
     *
     * @since 0.3
     * @param array $details
     */
    public function setDetails($details)
    {
        $this->details = $details;
    }

    /**
     * Get the raw post
     *
     * @since 0.3
     * @return \WP_Post
     */
    public function getRawPost()
    {
        return $this->post;
    }
}
