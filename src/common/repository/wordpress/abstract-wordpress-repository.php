<?php
namespace Affilicious\Common\Repository\Wordpress;

if (!defined('ABSPATH')) {
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
}
