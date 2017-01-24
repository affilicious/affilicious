<?php
namespace Affilicious\Product\Model;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Review_Aware_Interface
{
    /**
     * Check if the product has a review.
     *
     * @since 0.8
     * @return bool
     */
    public function has_review();

    /**
     * Get the optional product review.
     *
     * @since 0.8
     * @return null|Review
     */
    public function get_review();

    /**
     * Set the optional product review.
     *
     * @since 0.8
     * @param null|Review $review
     */
    public function set_review(Review $review = null);
}
