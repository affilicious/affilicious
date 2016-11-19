<?php
namespace Affilicious\Product\Application\Updater\Response;

use Affilicious\Common\Application\Queue\Min_Priority_Queue;
use Affilicious\Product\Application\Updater\Abstract_Update_Queue;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Update_Response_Queue extends Abstract_Update_Queue implements Update_Response_Queue_Interface
{
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
    public function put(Update_Response_Interface $update_response)
    {
        $updated_at = $update_response->get_shop()->get_updated_at()->getTimestamp();
        $this->min_priority_queue->insert($update_response, $updated_at);
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
                'The given number of update responses %d is out of range. It has to be between 1 and 100',
                $number
            ));
        }

        $batch_update_response = new Batch_Update_Response();
        for($i = 0; $i < $number; $i++) {
            $update_response = $this->min_priority_queue->extract();
            $batch_update_response->insert($update_response);
        }

        return $batch_update_response;
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
