<?php
namespace Affilicious\Provider\Model;

use Affilicious\Common\Model\Custom_Value_Aware_Trait;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Name_Aware_Trait;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Slug_Aware_Trait;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Provider
{
    use Name_Aware_Trait, Slug_Aware_Trait, Custom_Value_Aware_Trait;

    /**
     * The unique and optional ID of the provider.
     *
     * @var Provider_Id
     */
    protected $id;

    /**
     * The credentials contains all necessary information to build an API request
     *
     * @var Credentials
     */
    protected $credentials;

	/**
	 * The type like "amazon", "ebay" or "affilinet".
	 * Very useful, if you have multiple providers of the same type.
	 * It's optional now, but will be required in feature versions.
	 *
	 * @var Type|null
	 */
    protected $type;

	/**
	 * @since 0.7
	 * @param Name $name The provider name.
	 * @param Slug $slug The provider slug.
	 * @param Credentials $credentials The credentials containing all necessary information to build an API request.
	 * @param Type|null $type The type like "amazon", "ebay" or "affilinet". Very useful, if you have multiple providers of the same type. It's optional now, but will be required in feature versions.
	 */
    public function __construct(Name $name, Slug $slug, Credentials $credentials, Type $type = null)
    {
        $this->set_name($name);
        $this->set_slug($slug);
        $this->credentials = $credentials;
        $this->type = $type;
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
     * Get the credentials containing all necessary information to build an API request.
     *
     * @since 0.7
     * @return Credentials
     */
    public function get_credentials()
    {
        return $this->credentials;
    }

	/**
	 * Get the type like "amazon", "ebay" or "affilinet".
	 * Very useful, if you have multiple providers of the same type.
	 * It's optional now, but will be required in feature versions.
	 *
	 * @since 0.9.7
	 * @return Type|null
	 */
    public function get_type()
    {
		return $this->type;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function is_equal_to($other)
    {
        return
	        $other instanceof self &&
	        $this->get_name()->is_equal_to($other->get_name()) &&
	        $this->get_slug()->is_equal_to($other->get_slug()) &&
	        $this->get_credentials()->is_equal_to($other->get_credentials()) &&
	        ($this->get_type() !== null && $this->get_type()->is_equal_to($other->get_type()) || $other->get_type() === null);
    }
}
