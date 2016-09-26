<?php
namespace Affilicious\Product\Domain\Model\Review;

use Affilicious\Common\Domain\Exception\InvalidTypeException;
use Affilicious\Common\Domain\Model\AbstractValueObject;
use Affilicious\Product\Domain\Exception\InvalidBigNumberException;
use Affilicious\Product\Domain\Exception\InvalidSmallNumberException;

class Rating extends AbstractValueObject
{
    const MIN = 0;
    const MAX = 5;

    /**
     * Get a rating with the min value
     *
     * @since 0.5.2
     * @return Rating
     */
    public static function getMin()
    {
        return new self(self::MIN);
    }

    /**
     * Get a rating with the max value
     *
     * @since 0.5.2
     * @return Rating
     */
    public static function getMax()
    {
        return new self(self::MAX);
    }

    /**
     * @inheritdoc
     * @since 0.5.2
     * @throws InvalidTypeException
     * @throws InvalidSmallNumberException
     * @throws InvalidBigNumberException
     */
    public function __construct($value)
    {
        if (!is_int($value)) {
            throw new InvalidTypeException($value, 'int');
        }

        if($value < self::MIN) {
            throw new InvalidSmallNumberException($value, self::MIN);
        }

        if($value > self::MAX) {
            throw new InvalidBigNumberException($value, self::MAX);
        }

        parent::__construct($value);
    }
}
