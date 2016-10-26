<?php
namespace Affilicious\Common\Domain\Model\Image;

use Affilicious\Common\Domain\Model\Abstract_Aggregate;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Image extends Abstract_Aggregate
{
    /**
     * @var Image_Id
     */
    protected $id;

    /**
     * @var Source
     */
    protected $source;

    /**
     * @var Width
     */
    protected $width;

    /**
     * @var Height
     */
    protected $height;

    /**
     * @since 0.6
     * @param Image_Id $id
     * @param Source $source
     */
    public function __construct(Image_Id $id, Source $source)
    {
        $this->id = $id;
        $this->source = $source;
    }

    /**
     * Get the ID
     *
     * @since 0.6
     * @return Image_Id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Get the image source
     *
     * @since 0.6
     * @return Source
     */
    public function get_source()
    {
        return $this->source;
    }

    /**
     * Check if the image has a defined width
     *
     * @since 0.6
     * @return bool
     */
    public function has_width()
    {
        return $this->width !== null;
    }

    /**
     * Get the image width
     *
     * @since 0.6
     * @return Width
     */
    public function get_width()
    {
        return $this->width;
    }

    /**
     * Set the image width
     *
     * @since 0.6
     * @param Width $width
     */
    public function set_width($width)
    {
        $this->width = $width;
    }

    /**
     * Check if the image has a defined height
     *
     * @since 0.6
     * @return bool
     */
    public function has_height()
    {
        return $this->height !== null;
    }

    /**
     * Get the image height
     *
     * @since 0.6
     * @return Height
     */
    public function get_height()
    {
        return $this->height;
    }

    /**
     * Set the image height
     *
     * @since 0.6
     * @param Height $height
     */
    public function set_height($height)
    {
        $this->height = $height;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function is_equal_to($object)
    {
        return
            $object instanceof self &&
            $this->get_id()->is_equal_to($object);
    }
}
