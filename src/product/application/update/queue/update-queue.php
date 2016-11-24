<?php
namespace Affilicious\Product\Application\Update\Queue;

use Affilicious\Common\Application\Queue\Min_Priority_Queue;
use Affilicious\Product\Application\Update\Task\Update_Task_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Update_Queue implements Update_Queue_Interface
{
    /**
     * @var string
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
    public function __construct($name)
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
        $updated_at = $update_task->get_product()->get_updated_at()->getTimestamp();
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

        $result = array();
        for($i = 0; $i < $number; $i++) {
            if (!$this->is_empty()) {
                $update_task = $this->min_priority_queue->extract();
                $result[] = $update_task;
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
