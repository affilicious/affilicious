<?php
namespace Affilicious\Product\Update\Queue;

use Affilicious\Common\Queue\Min_Priority_Queue;
use Affilicious\Common\Model\Slug;
use Affilicious\Product\Update\Task\Update_Task;
use Affilicious\Product\Update\Task\Update_Task_Interface;
use Affilicious\Product\Model\Shop_Aware_Product_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Update_Queue implements Update_Queue_Interface
{
    /**
     * @var Slug
     */
    protected $name;

    /**
     * @var Min_Priority_Queue
     */
    protected $min_priority_queue;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function __construct(Slug $name)
    {
        $this->name = $name;
        $this->min_priority_queue = new Min_Priority_Queue();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function put(Update_Task_Interface $update_task)
    {
        $product = $update_task->get_product();
        $shops = $product instanceof Shop_Aware_Product_Interface ? $product->get_shops() : array();

        $updated_at = $product->get_updated_at()->getTimestamp();
        foreach ($shops as $shop) {
            if($shop->get_updated_at()->getTimestamp() < $updated_at) {
                $updated_at = $shop->get_updated_at()->getTimestamp();
            }
        }

        $this->min_priority_queue->insert($update_task, $updated_at);
    }

    /**
     * @inheritdoc
     * @since 0.7
     * @throws \OutOfRangeException
     */
    public function get($number = 1)
    {
        if($number < 1 || $number > 100) {
            throw new \OutOfRangeException(sprintf(
                'The given number of update requests %d is out of range. It has to be between %d and %d',
                $number,
                self::MIN,
                self::MAX
            ));
        }

        if($this->is_empty()) {
            return array();
        }

        $already_used = array();
        $result = array();
        for($i = 0; $i < $number; $i++) {
            if (!$this->is_empty()) {
                /** @var Update_Task $update_task */
                $update_task = $this->min_priority_queue->extract();
                $product_id = $update_task->get_product()->get_id();

                // We don't want to use the same update task twice...
                if(in_array($product_id->get_value(), $already_used)) {
                    continue;
                }

                $result[] = $update_task;
                $already_used[] = $product_id->get_value();
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_size()
    {
        $count = $this->min_priority_queue->count();

        return $count;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function is_empty()
    {
        $empty = $this->get_size() == 0;

        return $empty;
    }
}
