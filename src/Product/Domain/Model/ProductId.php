<?php
namespace Affilicious\Product\Domain\Model;

use Affilicious\Common\Domain\Exception\InvalidTypeException;
use Affilicious\Common\Domain\Model\AbstractValueObject;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class ProductId extends AbstractValueObject
{
    /**
     * @inheritdoc
     * @since 0.6
     * @throws InvalidTypeException
     */
    public function __construct($value)
    {
        if (is_numeric($value)) {
            $value = intval($value);
        }

        if (!is_int($value)) {
            throw new InvalidTypeException($value, 'int');
        }

        parent::__construct($value);
    }
}
