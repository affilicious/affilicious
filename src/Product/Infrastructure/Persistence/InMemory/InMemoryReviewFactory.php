<?php
namespace Affilicious\Product\Infrastructure\Persistence\InMemory;

use Affilicious\Product\Domain\Model\Review\Rating;
use Affilicious\Product\Domain\Model\Review\Review;
use Affilicious\Product\Domain\Model\Review\ReviewFactoryInterface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class InMemoryReviewFactory implements ReviewFactoryInterface
{
    /**
     * @inheritdoc
     */
    public function create(Rating $rating)
    {
        $review = new Review($rating);

        return $review;
    }
}
