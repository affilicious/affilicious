<?php
namespace Affilicious\Product\Application\Updater;

use Affilicious\Common\Domain\Model\Value_Object_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

abstract class Abstract_Update_Queue implements Update_Queue_Interface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function __construct($name)
    {
        if($name instanceof Value_Object_Interface) {
            $name = $name->get_value();
        }

        if(!is_string($name)) {
            throw new \InvalidArgumentException(sprintf(
                'The name "%s" has to be of type string.',
                $name
            ));
        }

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
