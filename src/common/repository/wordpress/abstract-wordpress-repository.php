<?php
namespace Affilicious\Common\Repository\Wordpress;

use Affilicious\Common\Model\Image\Height;
use Affilicious\Common\Model\Image\Image;
use Affilicious\Common\Model\Image\Image_Id;
use Affilicious\Common\Model\Image\Source;
use Affilicious\Common\Model\Image\Width;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

abstract class Abstract_Wordpress_Repository
{
    const THUMBNAIL_ID = '_thumbnail_id';

    /**
     * Add or update the post meta
     *
     * @since 0.6
     * @param mixed $id
     * @param mixed $key
     * @param mixed $value
     * @param bool $unique
     * @return bool|int
     */
    protected function store_post_meta($id, $key, $value, $unique = true)
    {
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
     * @param mixed $id
     * @param mixed $key
     * @return bool|int
     */
    protected function delete_post_meta($id, $key)
    {
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
