<?php
namespace Affilicious\Shop\Domain\Model\Provider;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

abstract class Abstract_Credentials implements Credentials_Interface
{
    /**
     * @var array
     */
    protected $credentials;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function __construct($credentials)
    {
        $this->credentials;
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
            $this->get_credentials() == $object->get_credentials();
    }
}
