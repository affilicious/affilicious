<?php
namespace Affilicious\Product\Update\Queue;

use Affilicious\Common\Queue\Min_Priority_Queue;
use Affilicious\Common\Model\Slug;
use Affilicious\Product\Model\Shop_Aware_Interface;
use Affilicious\Product\Update\Task\Batch_Update_Task;
use Affilicious\Product\Update\Task\Update_Task;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Update_Queue implements Update_Queue_Interface
{
    /**
     * @var Slug
     */
    private $name;

    /**
     * @var Min_Priority_Queue
     */
    private $min_priority_queue;

    /**
     * @inheritdoc
     * @since 0.9
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->min_priority_queue = new Min_Priority_Queue();
    }

    /**
     * @inheritdoc
     * @since 0.9
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function put(Update_Task $update_task)
    {
        $product = $update_task->get_product();
        $shops = $product instanceof Shop_Aware_Interface ? $product->get_shops() : array();

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
     * @since 0.9
     */
    public function put_batched(Batch_Update_Task $batch_update_task)
    {
        $provider = $batch_update_task->get_provider();
        $products = $batch_update_task->get_products();
        foreach ($products as $product) {
            $update_task = new Update_Task($provider, $product);
            $this->put($update_task);
        }
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get($number = 1)
    {
        if($number < 1 || $number > 100) {
            return new \WP_Error('aff_product_update_queue_number_out_of_range', sprintf(
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
     * @since 0.9
     */
    public function get_batched($number = 10)
    {
        $update_tasks = $this->get($number);
        if($update_tasks instanceof \WP_Error) {
            return $update_tasks;
        }

        if(empty($update_tasks)) {
            return null;
        }

        $provider = $update_tasks[0]->get_provider();

        $batch_update_task = new Batch_Update_Task($provider);
        foreach ($update_tasks as $update_task) {
            $product = $update_task->get_product();
            $batch_update_task->add_product($product);
        }

        return $batch_update_task;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_size()
    {
        return $this->min_priority_queue->count();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function is_empty()
    {
        return $this->get_size() == 0;
    }
}
