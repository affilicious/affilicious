<?php
namespace Affilicious\Product\Domain\Model\Review;

use Affilicious\Common\Domain\Exception\InvalidTypeException;
use Affilicious\Common\Domain\Model\AbstractAggregate;

class Review extends AbstractAggregate
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
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * Check if the review has any votes
     *
     * @since 0.6
     * @return bool
     */
    public function hasVotes()
    {
        return $this->votes !== null;
    }

    /**
     * Get the optional number of votes
     *
     * @since 0.6
     * @return null|Votes
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * Set the optional number of votes
     *
     * @since 0.6
     * @param null|Votes $votes
     */
    public function setVotes($votes)
    {
        if($votes !== null && !($votes instanceof Votes)) {
            throw new InvalidTypeException($votes, 'Affilicious\Product\Domain\Model\Review\Votes');
        }

        $this->votes = $votes;
    }

    /**
     * @inheritdoc
     */
    public function isEqualTo($object)
    {
        return
            $object instanceof self &&
            $this->getRating()->isEqualTo($object->getRating()) &&
            ($this->hasVotes() && $this->getVotes()->isEqualTo($object->getVotes()) || !$object->hasVotes());
    }
}
