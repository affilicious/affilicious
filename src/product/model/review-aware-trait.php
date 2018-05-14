<?php
namespace Affilicious\Product\Model;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
trait Review_Aware_Trait
{
    /**
     * @since 0.8
     * @var null|Review
     */
	protected $review;

    /**
     * Check if the product has a review.
     *
     * @since 0.8
     * @return bool
     */
    public function has_review()
    {
        return !empty($this->review);
    }

    /**
     * Get the optional product review.
     *
     * @since 0.8
     * @return null|Review
     */
    public function get_review()
    {
        return $this->review;
    }

    /**
     * Set the optional product review.
     *
     * @since 0.8
     * @param null|Review $review
     */
    public function set_review(Review $review = null)
    {
        $this->review = $review;
    }
}
