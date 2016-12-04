<?php
namespace Affilicious\Product\Application\Update\Worker;

use Affilicious\Common\Domain\Model\Name;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

abstract class Abstract_Update_Worker implements Update_Worker_Interface
{
    /**
     * @var Name
     */
    protected $name;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function __construct(Name $name)
    {
        $this->name = $name;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_name()
    {
        return $this->name;
    }
}
