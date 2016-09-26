<?php
namespace Affilicious\Detail\Domain\Model;

use Affilicious\Common\Application\Helper\DatabaseHelper;
use Affilicious\Detail\Domain\Model\Detail\Detail;
use Affilicious\Detail\Domain\Model\Detail\Key as DetailKey;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}


class DetailGroup
{
    const POST_TYPE = 'detail_group';

	/**
	 * @var DetailGroupId
	 */
	private $id;

    /**
     * @var Key
     */
    private $key;

	/**
	 * @var Title
	 */
	private $title;

    /**
     * @var Detail[]
     */
    private $details;

    /**
     * @since 0.6
     * @param DetailGroupId $id
     * @param Key $key
     * @param Title $title
     */
    public function __construct(DetailGroupId $id, Key $key, Title $title)
    {
	    $this->id = $id;
        $this->key = $key;
        $this->title = $title;
        $this->details = array();
    }

    /**
     * @since 0.6
     * @return DetailGroupId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @since 0.6
     * @return Title
     */
    public function getTitle()
    {
        return $this->title;
    }

	/**
	 * @return Key
	 */
	public function getKey()
	{
		return $this->key;
	}

    /**
     * Add a new detail
     *
     * @since 0.6
     * @param Detail $detail
     */
    public function addDetail(Detail $detail)
    {
        $this->details[] = $detail;
    }

    /**
     * Remove an existing detail by the key
     *
     * @since 0.6
     * @param DetailKey $key
     */
    public function removeDetail(DetailKey $key)
    {
        foreach ($this->details as $index => $detail) {
        	if($detail->getKey()->isEqualTo($key)) {
		        unset($this->details[$index]);
		        break;
	        }
        }
    }

    /**
     * Check if a detail with the given key exists
     *
     * @since 0.6
     * @param DetailKey $key
     * @return bool
     */
    public function hasDetail(DetailKey $key)
    {
        foreach ($this->details as $detail) {
	        if($detail->getKey()->isEqualTo($key)) {
		        return true;
	        }
        }

        return false;
    }

    /**
     * Get an existing detail by the key
     * You don't need to check for the key, but you will get null on non-existence
     *
     * @since 0.3
     * @param DetailKey $key
     * @return null|Detail
     */
    public function getDetail(DetailKey $key)
    {
        foreach ($this->details as $detail) {
	        if($detail->getKey()->isEqualTo($key)) {
		        return $detail;
	        }
        }

        return null;
    }

    /**
     * Get all details
     *
     * @since 0.6
     * @return Detail[]
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set all details
     *
     * @since 0.6
     * @param Detail[] $details
     */
    public function setDetails($details)
    {
    	// addDetail checks for the type
    	foreach ($details as $detail) {
    		$this->addDetail($detail);
	    }
    }

    /**
     * Get the raw post
     *
     * @since 0.3
     * @return null|\WP_Post
     */
    public function getRawPost()
    {
        return get_post($this->id->getValue());
    }
}
