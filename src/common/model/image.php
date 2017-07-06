<?php
namespace Affilicious\Common\Model;

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
}
