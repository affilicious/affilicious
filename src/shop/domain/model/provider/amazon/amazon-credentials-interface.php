<?php
namespace Affilicious\Shop\Domain\Model\Provider\Amazon;

use Affilicious\Shop\Domain\Model\Provider\Credentials_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Amazon_Credentials_Interface extends Credentials_Interface
{
    /**
     * Get the access key id
     *
     * @since 0.7
     * @return Access_Key_Id
     */
    public function get_access_key_id();

    /**
     * Get the secret access key
     *
     * @since 0.7
     * @return Secret_Access_Key
     */
    public function get_secret_access_key();

    /**
     * Get the country
     *
     * @since 0.7
     * @return Country
     */
    public function get_country();

    /**
     * Get the partner tag
     *
     * @since 0.7
     * @return Partner_Tag
     */
    public function get_partner_tag();
}
