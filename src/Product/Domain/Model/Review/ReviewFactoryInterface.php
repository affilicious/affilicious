<?php
namespace Affilicious\Product\Domain\Model\Review;

use Affilicious\Common\Domain\Model\FactoryInterface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface ReviewFactoryInterface extends FactoryInterface
{
    /**
     * Create a completely new review which can be stored into the database.
     *
     * @since 0.6
     * @param Rating $rating
     * @return Review
     */
    public function create(Rating $rating);
}
