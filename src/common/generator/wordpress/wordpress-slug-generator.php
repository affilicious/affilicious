<?php
namespace Affilicious\Common\Generator\Wordpress;

use Affilicious\Common\Model\Slug;
use Affilicious\Common\Generator\Slug_Generator_Interface;
use Affilicious\Common\Model\Name;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
class Wordpress_Slug_Generator implements Slug_Generator_Interface
{
    /**
     * @inheritdoc
     * @since 0.8
     */
    public function generate_from_name(Name $name)
    {
        // Make it to lower case and many more.
        $value = sanitize_title($name->get_value());

        // Wrap it up
        $slug = new Slug($value);

        return $slug;
    }
}
