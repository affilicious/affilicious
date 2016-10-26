<?php
namespace Affilicious\Common\Domain\Model;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

abstract class Abstract_Entity implements Entity_interface
{
	/**
	 * @var Value_Object_Interface
	 */
	protected $id;

    /**
     * @inheritdoc
     */
    public function get_id()
    {
        return $this->id;
    }
}
