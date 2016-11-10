<?php
namespace Affilicious\Shop\Domain\Model\Provider;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

abstract class Abstract_Provider implements Provider_Interface
{
    /**
     * @var Credentials_Interface
     */
    protected $credentials;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function __construct(Credentials_Interface $credentials)
    {
        $this->credentials = $credentials;
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
            $this->get_credentials()->is_equal_to($object->get_credentials());
    }
}
