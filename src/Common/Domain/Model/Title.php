<?php
namespace Affilicious\Common\Domain\Model;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Title extends Abstract_Value_Object
{
	/**
	 * @inheritdoc
	 * @since 0.6
	 * @throws Invalid_Type_Exception
	 */
	public function __construct($value)
	{
		if (!is_string($value)) {
			throw new Invalid_Type_Exception($value, 'string');
		}

		parent::__construct($value);
	}

    /**
     * Convert the title into a name
     *
     * @since 0.6
     * @return Name
     */
	public function to_name()
    {
        $name = new Name(sanitize_title($this->value));

        return $name;
    }

    /**
     * Convert the title into a key
     *
     * @since 0.6
     * @return Key
     */
    public function to_key()
    {
        $key = str_replace(' ', '_', $this->value);

        // _names cannot contain underscores followed by digits if you want to support carbon fields
        $key = preg_replace('/_([0-9])/', '$1', $key);

        $key = sanitize_title($key);
        $key = new Key($key);

        return $key;
    }
}
