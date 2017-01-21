<?php
namespace Affilicious\Provider\Factory;

use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Provider\Model\Amazon\Amazon_Provider;
use Affilicious\Provider\Model\Credentials;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Amazon_Provider_Factory_Interface extends Provider_Factory_Interface
{
    /**
     * @inheritdoc
     * @since 0.8
     * @return Amazon_Provider
     */
    public function create(Name $name, Slug $slug, Credentials $credentials);

    /**
     * @inheritdoc
     * @since 0.8
     * @return Amazon_Provider
     */
    public function create_from_name(Name $name, Credentials $credentials);
}
