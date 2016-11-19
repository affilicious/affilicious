<?php
namespace Affilicious\Product\Application\Updater\Request;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Batch_Update_Request implements Batch_Update_Request_Interface
{
    /**
     * @var Update_Request_Interface[]
     */
    private $batch_request;

    /**
     * @since 0.7
     * Batch_Update_Request constructor.
     */
    public function __construct()
    {
        $this->batch_request = array();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function insert(Update_Request_Interface $update_request)
    {
        $product_id = $update_request->get_product()->get_id();
        if(!isset($this->batch_request[$product_id->get_value()])) {
            $this->batch_request[$product_id->get_value()] = array();
        }

        $affiliate_link = $update_request->get_shop()->get_affiliate_link();
        $this->batch_request[$product_id->get_value()][$affiliate_link->get_value()] = $update_request;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function is_empty()
    {
        return empty($this->batch_request);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_size()
    {
        $count = 0;
        foreach ($this->batch_request as $product_id => $requests) {
            $count += count($requests);
        }

        return $count;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_requests()
    {
        $result = array();
        foreach ($this->batch_request as $product_id => $requests) {
            $result = array_merge($result, $requests);
        }

        return $result;
    }
}
