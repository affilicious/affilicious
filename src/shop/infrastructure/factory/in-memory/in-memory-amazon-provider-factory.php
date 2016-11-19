<?php
namespace Affilicious\Shop\Infrastructure\Factory\In_Memory;

use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Shop\Domain\Model\Provider\Amazon\Amazon_Provider;
use Affilicious\Shop\Domain\Model\Provider\Amazon\Amazon_Provider_Factory_Interface;
use Affilicious\Shop\Domain\Model\Provider\Credentials;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Amazon_Provider_Factory implements Amazon_Provider_Factory_Interface
{
    /**
     * @inheritdoc
     * @since 0.7
     */
    public function create(Title $title, Name $name, Key $key, Credentials $credentials)
    {
        do_action('affilicious_shop_provider_before_create_amazon');
        do_action('affilicious_shop_provider_before_create');

        $amazon_provider = new Amazon_Provider($title, $name, $key, $credentials);

        do_action('affilicious_shop_provider_after_create_amazon', $amazon_provider);
        do_action('affilicious_shop_provider_after_create', $amazon_provider);

        return $amazon_provider;
    }
}
