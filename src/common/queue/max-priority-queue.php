<?php
namespace Affilicious\Common\Queue;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.7
 */
class Max_Priority_Queue extends \SplPriorityQueue
{
    /**
     * Compare both priority by the biggest value
     *
     * @since 0.7
     * @param int $priority1
     * @param int $priority2
     * @return int
     */
    public function compare($priority1, $priority2)
    {
        if ($priority1 === $priority2) return 0;

        return $priority1 < $priority2 ? -1 : 1;
    }
}
