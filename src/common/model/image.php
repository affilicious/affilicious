<?php
namespace Affilicious\Common\Model;

use Affilicious\Common\Helper\Assert_Helper;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Image extends Image_Id
{
    /**
     * @var null|int
     */
    protected $id;

    /**
     * @var null|string
     */
    protected $src;

    /**
     * @inheritdoc
     * @since 0.9
     */
    public function __construct($id = null, $src = null)
    {
        if (is_numeric($id) || is_string($id)) {
            $id = intval($id);
        }

        Assert_Helper::is_integer_or_null($id, __METHOD__, 'The ID must be an integer or null. Got: %s', '0.9.2');
        Assert_Helper::is_string_not_empty_or_null($src, __METHOD__, 'The source must be a non empty string or null. Got: %s', '0.9.2');

        $this->id = $id;
        $this->src = $src;
    }

    /**
     * @deprecated 1.0
     * @since 0.9
     * @return int
     */
    public function get_value()
    {
        return $this->id;
    }

    /**
     * @since 0.9
     * @return int|null
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * @since 0.9
     * @return null|string
     */
    public function get_src()
    {
        return $this->src;
    }

    /**
     * @inheritdoc
     * @since 0.9
     */
    public function is_equal_to($other)
    {
        return
            $other instanceof self &&
            $this->get_id() == $other->get_id() &&
            $this->get_src() == $other->get_src();
    }
}
