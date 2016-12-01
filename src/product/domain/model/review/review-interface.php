<?php
namespace Affilicious\Product\Domain\Model\Review;

use Affilicious\Common\Domain\Model\Aggregate_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Review_Interface extends Aggregate_Interface
{
    /**
     * @since 0.7
     * @param Rating $rating
     */
    public function __construct(Rating $rating);

    /**
     * Get the rating.
     *
     * @since 0.7
     * @return Rating
     */
    public function get_rating();

    /**
     * Check if the review has any votes.
     *
     * @since 0.7
     * @return bool
     */
    public function has_votes();

    /**
     * Get the optional number of votes.
     *
     * @since 0.7
     * @return null|Votes
     */
    public function get_votes();

    /**
     * Set the optional number of votes.
     *
     * @since 0.7
     * @param null|Votes $votes
     */
    public function set_votes($votes);
}
