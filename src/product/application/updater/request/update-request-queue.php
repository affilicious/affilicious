<?php
namespace Affilicious\Product\Application\Updater\Request;

use Affilicious\Common\Application\Queue\Min_Priority_Queue;
use Affilicious\Product\Application\Updater\Abstract_Update_Queue;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Update_Request_Queue extends Abstract_Update_Queue implements Update_Request_Queue_Interface
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
        parent::__construct($name);
        $this->min_priority_queue = new Min_Priority_Queue();
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function put(Update_Request_Interface $update_request)
    {
        $updated_at = $update_request->get_shop()->get_updated_at()->getTimestamp();
        $this->min_priority_queue->insert($update_request, $updated_at);
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
                'The given number of update requests %d is out of range. It has to be between 1 and 100',
                $number
            ));
        }

        $batch_update_request = new Batch_Update_Request();
        for($i = 0; $i < $number; $i++) {
            $update_request = $this->min_priority_queue->extract();
            $batch_update_request->insert($update_request);
        }

        return $batch_update_request;
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
        return $this->get_size() == 0;
    }
}
