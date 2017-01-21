<?php
namespace Affilicious\Product\Model;

use Affilicious\Product\Model\Review\Review_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Review_Aware_Product_Interface extends Product_Interface
{
    /**
     * Check if the product has a review.
     *
     * @since 0.7
     * @return bool
     */
    public function has_review();

    /**
     * Get the optional review.
     *
     * @since 0.7
     * @return null|Review_Interface
     */
    public function get_review();

    /**
     * Set the optional review.
     *
     * @since 0.7
     * @param null|Review_Interface $review
     */
    public function set_review($review);
}
