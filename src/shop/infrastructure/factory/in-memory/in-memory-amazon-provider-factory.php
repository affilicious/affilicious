<?php
namespace Affilicious\Shop\Infrastructure\Factory\In_Memory;

use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Shop\Domain\Model\Provider\Amazon\Amazon_Provider;
use Affilicious\Shop\Domain\Model\Provider\Credentials;
use Affilicious\Shop\Domain\Model\Provider\Provider_Factory_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Amazon_Provider_Factory implements Provider_Factory_Interface
{
    /**
     * @inheritdoc
     * @since 0.7
     */
    public function create(Title $title, Name $name, Key $key, Credentials $credentials)
    {
        $amazon_provider = new Amazon_Provider($title, $name, $key, $credentials);

        return $amazon_provider;
    }
}
