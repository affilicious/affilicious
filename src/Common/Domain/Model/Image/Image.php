<?php
namespace Affilicious\Common\Domain\Model\Image;

use Affilicious\Common\Domain\Model\AbstractAggregate;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Image extends AbstractAggregate
{
    /**
     * @var ImageId
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
     * @param ImageId $id
     * @param Source $source
     */
    public function __construct(ImageId $id, Source $source)
    {
        $this->id = $id;
        $this->source = $source;
    }

    /**
     * Get the ID
     *
     * @since 0.6
     * @return ImageId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the image source
     *
     * @since 0.6
     * @return Source
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Check if the image has a defined width
     *
     * @since 0.6
     * @return bool
     */
    public function hasWidth()
    {
        return $this->width !== null;
    }

    /**
     * Get the image width
     *
     * @since 0.6
     * @return Width
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set the image width
     *
     * @since 0.6
     * @param Width $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * Check if the image has a defined height
     *
     * @since 0.6
     * @return bool
     */
    public function hasHeight()
    {
        return $this->height !== null;
    }

    /**
     * Get the image height
     *
     * @since 0.6
     * @return Height
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set the image height
     *
     * @since 0.6
     * @param Height $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function isEqualTo($object)
    {
        return
            $object instanceof self &&
            $this->getId()->isEqualTo($object);
    }
}
