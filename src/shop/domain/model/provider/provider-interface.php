<?php
namespace Affilicious\Shop\Domain\Model\Provider;

use Affilicious\Common\Domain\Model\Entity_Interface;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Provider_Interface extends Entity_Interface
{
    /**
     * @since 0.7
     * @param Title $title
     * @param Name $name
     * @param Key $key
     * @param Credentials $credentials
     */
    public function __construct(Title $title, Name $name, Key $key, Credentials $credentials);

    /**
     * Check if the provider has an optional ID
     *
     * @since 0.6
     * @return bool
     */
    public function has_id();

    /**
     * Get the optional provider ID
     *
     * @since 0.6
     * @return null|Provider_Id
     */
    public function get_id();

    /**
     * Set the optional provider ID
     *
     * @since 0.6
     * @param null|Provider_Id $id
     */
    public function set_id($id);

    /**
     * Get the title
     *
     * @since 0.7
     * @return Title
     */
    public function get_title();

    /**
     * Get the name
     *
     * @since 0.7
     * @return Name
     */
    public function get_name();

    /**
     * Get the key
     *
     * @since 0.7
     * @return Key
     */
    public function get_key();

    /**
     * Get the credentials
     *
     * @since 0.7
     * @return Credentials
     */
    public function get_credentials();
}
