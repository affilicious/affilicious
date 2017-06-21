<?php
namespace Affilicious\Common\Helper;

use Affilicious\Common\Model\Image_Id;

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
     * @return null|Image_Id
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

                return new Image_Id($attachment_id);
            }
        }

        return null;
    }

    /**
     * Delete the image by the ID.
     *
     * @since 0.9
     * @param Image_Id $id The ID of the image you would like to delete.
     * @param bool $force_delete Whether to bypass trash and force deletion. Default: false
     * @return bool Returns true if deletion has succeeded.
     */
    public static function delete(Image_Id $id, $force_delete = false)
    {
        $result = wp_delete_attachment($id->get_value(), $force_delete);

        return !(false === $result);
    }
}
