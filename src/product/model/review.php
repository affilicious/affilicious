<?php
namespace Affilicious\Product\Model;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
class Review
{
    /**
     * @since 0.8
     * @var Rating
     */
    protected $rating;

    /**
     * @since 0.8
     * @var null|Votes
     */
    protected $votes;

    /**
     * @since 0.8
     * @param Rating $rating
     * @param null|Votes $votes
     */
    public function __construct(Rating $rating, Votes $votes = null)
    {
        $this->rating = $rating;
        $this->votes = $votes;
    }

    /**
     * Get the rating
     *
     * @since 0.8
     * @return Rating
     */
    public function get_rating()
    {
        return $this->rating;
    }

    /**
     * Check if the review has any votes.
     *
     * @since 0.8
     * @return bool
     */
    public function has_votes()
    {
        return $this->votes !== null;
    }

    /**
     * Get the optional number of votes.
     *
     * @since 0.8
     * @return null|Votes
     */
    public function get_votes()
    {
        return $this->votes;
    }

    /**
     * Set the optional number of votes.
     *
     * @since 0.8
     * @param null|Votes $votes
     */
    public function set_votes(Votes $votes = null)
    {
        $this->votes = $votes;
    }

    /**
     * Check if this review is equal to the other one.
     *
     * @since 0.8
     * @param mixed $other
     * @return bool
     */
    public function is_equal_to($other)
    {
        return
            $other instanceof self &&
            $this->get_rating()->is_equal_to($other->get_rating()) &&
            ($this->has_votes() && $this->get_votes()->is_equal_to($other->get_votes()) || !$other->has_votes());
    }
}
