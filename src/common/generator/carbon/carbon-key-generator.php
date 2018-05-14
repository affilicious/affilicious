<?php
namespace Affilicious\Common\Generator\Carbon;

use Affilicious\Common\Generator\Key_Generator_Interface;
use Affilicious\Common\Model\Key;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
class Carbon_Key_Generator implements Key_Generator_Interface
{
    /**
     * @inheritdoc
     * @since 0.8
     */
    public function generate_from_name(Name $name)
    {
        // Keys cannot contain underscores followed by digits in Carbon Fields.
        $value = preg_replace('/ ([0-9])/', '$1', $name->get_value());

        // Make it to lower case and many more.
        $value = sanitize_title($value);

        // Unlike slugs, "-" isn't allowed in keys.
        $value = str_replace('-', '_', $value);

	    // Plain numbers are not allowed. Prefix with "n" which stand for number.
	    if(is_numeric($value)) {
		    $value = 'n' . $value;
	    }

        // Wrap it up
        $key = new Key($value);

        return $key;
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function generate_from_slug(Slug $slug)
    {
        // Keys cannot contain underscores followed by digits in Carbon Fields.
        $value = preg_replace('/-([0-9])/', '$1', $slug->get_value());

	    // Unlike slugs, "-" isn't allowed in keys.
        $value = str_replace('-', '_', $value);

        // Plain numbers are not allowed. Prefix with "n" which stand for number.
        if(is_numeric($value)) {
        	$value = 'n' . $value;
        }

        // Wrap it up
        $key = new Key($value);

        return $key;
    }
}
