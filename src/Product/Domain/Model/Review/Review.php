<?php
namespace Affilicious\Product\Domain\Model\Review;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Abstract_Aggregate;

class Review extends Abstract_Aggregate
{
    /**
     * @var Rating
     */
    private $rating;

    /**
     * @var null|Votes
     */
    private $votes;

    /**
     * @since 0.6
     * @param Rating $rating
     */
    public function __construct(Rating $rating)
    {
        $this->rating = $rating;
    }

    /**
     * Get the rating
     *
     * @since 0.6
     * @return Rating
     */
    public function get_rating()
    {
        return $this->rating;
    }

    /**
     * Check if the review has any votes
     *
     * @since 0.6
     * @return bool
     */
    public function has_votes()
    {
        return $this->votes !== null;
    }

    /**
     * Get the optional number of votes
     *
     * @since 0.6
     * @return null|Votes
     */
    public function get_votes()
    {
        return $this->votes;
    }

    /**
     * Set the optional number of votes
     *
     * @since 0.6
     * @param null|Votes $votes
     */
    public function set_votes($votes)
    {
        if($votes !== null && !($votes instanceof Votes)) {
            throw new Invalid_Type_Exception($votes, 'Affilicious\Product\Domain\Model\Review\Votes');
        }

        $this->votes = $votes;
    }

    /**
     * @inheritdoc
     */
    public function is_equal_to($object)
    {
        return
            $object instanceof self &&
            $this->get_rating()->is_equal_to($object->get_rating()) &&
            ($this->has_votes() && $this->get_votes()->is_equal_to($object->get_votes()) || !$object->has_votes());
    }
}
