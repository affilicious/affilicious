<?php
namespace Affilicious\Product\Model\Review;


if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Review_Factory_Interface
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
