<?php
namespace Affilicious\Common\Infrastructure\Persistence\Wordpress;

use Affilicious\Common\Domain\Model\Image\Height;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Image\ImageId;
use Affilicious\Common\Domain\Model\Image\Source;
use Affilicious\Common\Domain\Model\Image\Width;
use Affilicious\Common\Domain\Model\RepositoryInterface;
use Affilicious\Common\Domain\Model\ValueObjectInterface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

abstract class AbstractWordpressRepository implements RepositoryInterface
{
    /**
     * Add or update the post meta
     *
     * @since 0.6
     * @param mixed|ValueObjectInterface $id
     * @param mixed|ValueObjectInterface $key
     * @param mixed|ValueObjectInterface $value
     * @return bool|int
     */
    protected function storePostMeta($id, $key, $value)
    {
        if($id instanceof ValueObjectInterface) {
            $id = $id->getValue();
        }

        if($key instanceof ValueObjectInterface) {
            $key = $key->getValue();
        }

        if($value instanceof ValueObjectInterface) {
            $value = $value->getValue();
        }

        // Prefix the key with _
        if(strpos($key, '_') !== 0) {
            $key = '_' . $key;
        }

        $updated = update_post_meta($id, $key, $value);
        if(!$updated) {
            add_post_meta($id, $key, $value);
        }

        return $updated;
    }

    /**
     * Get the image from the attachment ID
     *
     * @since 0.6
     * @param int $attachmentId
     * @return null|Image
     */
    protected function getImageFromAttachmentId($attachmentId)
    {
        $attachment = wp_get_attachment_image_src($attachmentId);
        if(empty($attachment) && count($attachment) == 0) {
            return null;
        }

        $source = $attachment[0];
        if(empty($source)) {
            return null;
        }

        $image = new Image(
            new ImageId($attachmentId),
            new Source($source)
        );

        $width = $attachment[1];
        if(!empty($width)) {
            $image->setWidth(new Width($width));
        }

        $height = $attachment[2];
        if(!empty($height)) {
            $image->setHeight(new Height($height));
        }

        return $image;
    }
}
