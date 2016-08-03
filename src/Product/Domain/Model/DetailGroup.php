<?php
namespace Affilicious\ProductsPlugin\Product\Domain\Model;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class DetailGroup
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Detail[]
     */
    private $details;

    /**
     * @param int $id
     */
    public function __construct($id)
    {
        $this->id = $id;
        $this->details = array();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add a new detail
     * @param Detail $detail
     */
    public function addDetail(Detail $detail)
    {
        $this->details[$detail->getKey()] = $detail;
    }

    /**
     * Remove an existing detail by the key
     * @param string $key
     */
    public function removeDetail($key)
    {
        unset($this->details[$key]);
    }

    /**
     * Check if a detail with the given key exists
     * @param string $key
     * @return bool
     */
    public function hasDetail($key)
    {
        return isset($this->details[$key]);
    }

    /**
     * Get an existing detail by the key
     * You don't need to check for the key, but you will get null on non-existence
     * @param string $key
     * @return null|Detail
     */
    public function getDetail($key)
    {
        if (!$this->hasDetail($key)) {
            return null;
        }

        return $this->details[$key];
    }

    /**
     * Get all details
     * @return Detail[]
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Count the number of details
     * @return int
     */
    public function countDetails()
    {
        return count($this->getDetails());
    }
}
