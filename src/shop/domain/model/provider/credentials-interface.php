<?php
namespace Affilicious\Shop\Domain\Model\Provider;

use Affilicious\Common\Domain\Model\Aggregate_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Credentials_Interface extends Aggregate_Interface
{
    /**
     * @since 0.7
     * @param array $credentials
     */
    public function __construct($credentials);

    /**
     * Get the credentials
     *
     * @since 0.7
     * @return array
     */
    public function get_credentials();
}
