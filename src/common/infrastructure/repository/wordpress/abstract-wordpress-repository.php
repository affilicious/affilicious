<?php
namespace Affilicious\Common\Infrastructure\Repository\Wordpress;

use Affilicious\Common\Domain\Model\Image\Height;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Image\Image_Id;
use Affilicious\Common\Domain\Model\Image\Source;
use Affilicious\Common\Domain\Model\Image\Width;
use Affilicious\Common\Domain\Model\Repository_Interface;
use Affilicious\Common\Domain\Model\Value_Object_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

abstract class Abstract_Wordpress_Repository implements Repository_Interface
{
    const THUMBNAIL_ID = '_thumbnail_id';

    /**
     * Add or update the post meta
     *
     * @since 0.6
     * @param mixed|Value_Object_Interface $id
     * @param mixed|Value_Object_Interface $key
     * @param mixed|Value_Object_Interface $value
     * @param bool $unique
     * @return bool|int
     */
    protected function store_post_meta($id, $key, $value, $unique = true)
    {
        if($id instanceof Value_Object_Interface) {
            $id = $id->get_value();
        }

        if($key instanceof Value_Object_Interface) {
            $key = $key->get_value();
        }

        if($value instanceof Value_Object_Interface) {
            $value = $value->get_value();
        }

        // _prefix the key with _
        if(strpos($key, '_') !== 0) {
            $key = '_' . $key;
        }

        $updated = update_post_meta($id, $key, $value);
        if(!$updated) {
            add_post_meta($id, $key, $value, $unique);
        }

        return $updated;
    }

    /**
     * Delete the post meta
     *
     * @since 0.6
     * @param mixed|Value_Object_Interface $id
     * @param mixed|Value_Object_Interface $key
     * @return bool|int
     */
    protected function delete_post_meta($id, $key)
    {
        if($id instanceof Value_Object_Interface) {
            $id = $id->get_value();
        }

        if($key instanceof Value_Object_Interface) {
            $key = $key->get_value();
        }

        $deleted = delete_post_meta($id, $key);

        return $deleted;
    }

    /**
     * Get the image from the attachment ID
     *
     * @since 0.6
     * @param int $attachment_id
     * @return null|Image
     */
    protected function get_image_from_attachment_id($attachment_id)
    {
        $attachment = wp_get_attachment_image_src($attachment_id);
        if(empty($attachment) && count($attachment) == 0) {
            return null;
        }

        $source = $attachment[0];
        if(empty($source)) {
            return null;
        }

        $image = new Image(
            new Image_Id($attachment_id),
            new Source($source)
        );

        $width = $attachment[1];
        if(!empty($width)) {
            $image->set_width(new Width($width));
        }

        $height = $attachment[2];
        if(!empty($height)) {
            $image->set_height(new Height($height));
        }

        return $image;
    }
}
