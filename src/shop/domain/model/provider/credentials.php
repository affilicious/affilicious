<?php
namespace Affilicious\Shop\Domain\Model\Provider;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Abstract_Value_Object;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Credentials extends Abstract_Value_Object
{
    /**
     * @inheritdoc
     * @since 0.7
     */
    public function __construct($value)
    {
        if (!is_array($value)) {
            throw new Invalid_Type_Exception($value, 'array');
        }

        parent::__construct($value);
    }

    /**
     * Get the part of the credentials by the key.
     *
     * @since 0.7
     * @param string $key
     * @return null|string
     */
    public function get($key)
    {
        return isset($this->value[$key]) ? $this->value[$key] : null;
    }
}
