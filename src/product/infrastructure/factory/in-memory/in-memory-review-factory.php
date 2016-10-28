<?php
namespace Affilicious\Product\Infrastructure\Factory\In_Memory;

use Affilicious\Product\Domain\Model\Review\Rating;
use Affilicious\Product\Domain\Model\Review\Review;
use Affilicious\Product\Domain\Model\Review\Review_Factory_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Review_Factory implements Review_Factory_Interface
{
    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Rating $rating)
    {
        $review = new Review($rating);

        return $review;
    }
}
