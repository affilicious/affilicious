<?php
namespace Affilicious\Common\Helper;

use Affilicious\Common\Model\Image;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Image_Helper
{
    /**
     * Download the image from the file source url.
     *
     * @since 0.9
     * @param string $file
     * @return null|Image
     */
    public static function download($file)
    {
        $filename = basename($file);

        $upload_file = wp_upload_bits($filename, null, file_get_contents($file));
        if (!$upload_file['error']) {
            $wp_filetype = wp_check_filetype($filename, null);
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
                'post_content' => '',
                'post_status' => 'inherit');

            $attachment_id = wp_insert_attachment($attachment, $upload_file['file']);
            if (!is_wp_error($attachment_id)) {
                require_once(ABSPATH . 'wp-admin/includes/image.php');

                $attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_file['file']);
                wp_update_attachment_metadata($attachment_id,  $attachment_data);

                return new Image($attachment_id);
            }
        }

        return null;
    }

    /**
     * Delete the image by the ID.
     *
     * @since 0.9
     * @param Image $image The image you would like to delete.
     * @param bool $force_delete Whether to bypass trash and force deletion. Default: false
     * @return bool Returns true if deletion has succeeded.
     */
    public static function delete(Image $image, $force_delete = false)
    {
        if($image->get_id() === null) {
            return false;
        }

        $result = wp_delete_attachment($image->get_id(), $force_delete);

        return !(false === $result);
    }

    /**
     * Convert the image into an array.
     *
     * @since 0.9
     * @param Image $image
     * @return array
     */
    public static function to_array(Image $image)
    {
        $result = [
            'id' => $image->get_id(),
            'src' => $image->get_src(),
        ];

        return $result;
    }

    /**
     * Convert the array into an image.
     *
     * @param array $image
     * @return Image|null
     */
    public static function from_array(array $image)
    {
        if(empty($image['id']) && empty($image['src'])) {
            return null;
        }

        $result = new Image($image['id'], $image['src']);

        return $result;
    }
}
