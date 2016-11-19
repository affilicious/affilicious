<?php
namespace Affilicious\Product\Application\Updater\Response;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Batch_Update_Response implements Batch_Update_Response_Interface
{
    /**
     * @var Update_Response_Interface[]
     */
    private $batch_response;

    /**
     * @since 0.7
     * Batch_Update_Response constructor.
     */
    public function __construct()
    {
        $this->batch_response = array();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function insert(Update_Response_Interface $update_response)
    {
        $product_id = $update_response->get_product()->get_id();
        if(!isset($this->batch_response[$product_id->get_value()])) {
            $this->batch_response[$product_id->get_value()] = array();
        }

        $affiliate_link = $update_response->get_shop()->get_affiliate_link();
        $this->batch_response[$product_id->get_value()][$affiliate_link->get_value()] = $update_response;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function is_empty()
    {
        return empty($this->batch_response);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_size()
    {
        $count = 0;
        foreach ($this->batch_response as $product_id => $responses) {
            $count += count($responses);
        }

        return $count;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_responses()
    {
        $result = array();
        foreach ($this->batch_response as $product_id => $responses) {
            $result = array_merge($result, $responses);
        }

        return $result;
    }
}
