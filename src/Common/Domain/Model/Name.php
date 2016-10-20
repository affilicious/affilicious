<?php
namespace Affilicious\Common\Domain\Model;

use Affilicious\Common\Domain\Exception\InvalidTypeException;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

class Name extends AbstractValueObject
{
	/**
	 * @inheritdoc
	 * @since 0.6
	 * @throws InvalidTypeException
	 */
	public function __construct($value)
	{
		if (!is_string($value)) {
			throw new InvalidTypeException($value, 'string');
		}

		parent::__construct($value);
	}

    /**
     * Convert the name into a key
     *
     * @since 0.6
     * @return Key
     */
    public function toKey()
    {
        $key = str_replace('-', '_', $this->value);

        // Names cannot contain underscores followed by digits if you want to support carbon fields
        $key = preg_replace('/_([0-9])/', '$1', $key);

        $key = new Key($key);

        return $key;
    }
}
