<?php
namespace Affilicious\Shop\Domain\Model\Provider;

use Affilicious\Common\Domain\Model\Factory_Interface;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Provider_Factory_Interface extends Factory_Interface
{
    /**
     * Create a completely new provider.
     *
     * @since 0.7
     * @param Title $title
     * @param Name $name
     * @param Key $key
     * @param Credentials $credentials
     * @return Provider_Interface
     */
    public function create(Title $title, Name $name, Key $key, Credentials $credentials);
}
