<?php
namespace Affilicious\Common\Domain\Model;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

abstract class Abstract_Entity implements Entity_Interface
{
	/**
	 * @var Value_Object_Interface
	 */
	protected $id;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function has_id()
    {
        return $this->id !== null;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_id($id)
    {
        if($id !== null && !($id instanceof Value_Object_Interface)) {
            throw new Invalid_Type_Exception($id, 'Affilicious\Common\Domain\Model\Value_Object_Interface');
        }

        $this->id = $id;
    }
}
