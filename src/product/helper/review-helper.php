<?php
namespace Affilicious\Product\Helper;

use Affilicious\Product\Model\Review;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Review_Helper
{
    /**
     * Convert the review into an array.
     *
     * @since 0.9
     * @param Review $review
     * @return array
     */
    public static function to_array(Review $review)
    {
        $result = [
            'rating' => $review->get_rating()->get_value(),
            'votes' => $review->has_votes() ? $review->get_votes()->get_value() : null,
        ];

        return $result;
    }
}
