<?php
namespace Affilicious\Provider\Model;

use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Name_Aware_Trait;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Slug_Aware_Trait;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Provider
{
    use Name_Aware_Trait, Slug_Aware_Trait;

    /**
     * The unique and optional ID of the provider.
     *
     * @var Provider_Id
     */
    private $id;

    /**
     * @var Credentials
     */
    private $credentials;

    /**
     * @since 0.7
     * @param Name $name
     * @param Slug $slug
     * @param Credentials $credentials
     */
    public function __construct(Name $name, Slug $slug, Credentials $credentials)
    {
        $this->set_name($name);
        $this->set_slug($slug);
        $this->credentials = $credentials;
    }

    /**
     * Check if the provider has an unique ID.
     *
     * @since 0.8
     * @return bool
     */
    public function has_id()
    {
        return $this->id !== null;
    }

    /**
     * Get the unique ID of the provider.
     *
     * @since 0.8
     * @return null|Provider_Id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Set the unique ID of the provider.
     *
     * @since 0.8
     * @param null|Provider_Id $id
     */
    public function set_id(Provider_Id $id = null)
    {
        $this->id = $id;
    }

    /**
     * Get the credentials
     *
     * @since 0.7
     * @return Credentials
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
            $this->get_name()->is_equal_to($object->get_name()) &&
            $this->get_slug()->is_equal_to($object->get_slug()) &&
            $this->get_credentials()->is_equal_to($object->get_credentials());
    }
}
