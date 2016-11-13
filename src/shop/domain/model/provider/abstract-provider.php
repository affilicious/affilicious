<?php
namespace Affilicious\Shop\Domain\Model\Provider;

use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Abstract_Entity;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Shop\Domain\Model\Provider\Credentials;
use Affilicious\Shop\Domain\Model\Provider\Provider_Id;
use Affilicious\Shop\Domain\Model\Provider\Provider_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Abstract_Provider extends Abstract_Entity implements Provider_Interface
{
    /**
     * @var Title
     */
    protected $title;

    /**
     * @var Name
     */
    protected $name;

    /**
     * @var Key
     */
    protected $key;

    /**
     * @var Credentials
     */
    protected $credentials;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function __construct(Title $title, Name $name, Key $key, Credentials $credentials)
    {
        $this->title = $title;
        $this->name = $name;
        $this->key = $key;
        $this->credentials = $credentials;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function set_id($id)
    {
        if($id !== null && !($id instanceof Provider_Id)) {
            throw new Invalid_Type_Exception($id, 'Affilicious\Shop\Domain\Model\Provider\Provider_Id');
        }

        parent::set_id($id);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_title()
    {
        return $this->title;
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
    public function get_key()
    {
        return $this->key;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_credentials()
    {
        return $this->credentials;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function is_equal_to($object)
    {
        return
            $object instanceof self &&
            $this->get_title()->is_equal_to($object->get_title()) &&
            $this->get_name()->is_equal_to($object->get_name()) &&
            $this->get_key()->is_equal_to($object->get_key()) &&
            $this->get_credentials()->is_equal_to($object->get_credentials());
    }
}
