<?php
namespace Affilicious\Shop\Domain\Model\Provider;

use Affilicious\Common\Domain\Model\Aggregate_Interface;
use Affilicious\Shop\Domain\Model\Shop_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Provider_Interface extends Aggregate_Interface
{
    /**
     * @since 0.7
     * @param Credentials_Interface $credentials
     */
    public function __construct(Credentials_Interface $credentials);

    /**
     * Get the credentials
     *
     * @since 0.7
     * @return Credentials_Interface
     */
    public function get_credentials();

    /**
     * Update the shop data like the price
     *
     * @since 0.7
     * @param Shop_Interface $shop
     * @return Shop_Interface
     */
    public function update_shop(Shop_Interface $shop);
}
