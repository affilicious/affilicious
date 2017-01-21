<?php
namespace Affilicious\Provider\Factory;

use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Name;
use Affilicious\Provider\Model\Credentials;
use Affilicious\Provider\Model\Provider;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Provider_Factory_Interface
{
    /**
     * Create a new provider.
     *
     * @since 0.8
     * @param Name $name
     * @param Slug $slug
     * @param Credentials $credentials
     * @return Provider
     */
    public function create(Name $name, Slug $slug, Credentials $credentials);

    /**
     * Create a new provider.
     * The slug is auto-generated from the name.
     *
     * @since 0.8
     * @param Name $name
     * @param Credentials $credentials
     * @return Provider
     */
    public function create_from_name(Name $name, Credentials $credentials);
}
