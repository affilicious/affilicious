<?php
namespace Affilicious\Product\Domain\Model\Review;

use Affilicious\Common\Domain\Exception\InvalidTypeException;
use Affilicious\Common\Domain\Model\AbstractValueObject;
use Affilicious\Product\Domain\Exception\InvalidSmallNumberException;

class Votes extends AbstractValueObject
{
    const MIN = 0;

    /**
     * Get a votes with the min value
     *
     * @since 0.5.2
     * @return Votes
     */
    public static function getMin()
    {
        return new self(self::MIN);
    }

    /**
     * @since 0.5.2
     * @param mixed $value
     */
    public function __construct($value)
    {
        if (!is_int($value)) {
            throw new InvalidTypeException($value, 'int');
        }

        if($value < self::MIN) {
            throw new InvalidSmallNumberException($value, self::MIN);
        }

        parent::__construct($value);
    }
}
