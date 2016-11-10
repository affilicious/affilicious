<?php
namespace Affilicious\Shop\Domain\Model\Provider\Amazon;

use Affilicious\Shop\Domain\Model\Provider\Abstract_Provider;
use Affilicious\Shop\Domain\Model\Provider\Credentials_Interface;
use Affilicious\Shop\Domain\Model\Shop_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

abstract class Abstract_Amazon_Provider extends Abstract_Provider implements Amazon_Provider_Interface
{
    /**
     * @inheritdoc
     * @since 0.7
     */
    public function __construct(Credentials_Interface $credentials)
    {
        if(!($credentials instanceof Amazon_Credentials_Interface)) {
            throw new \InvalidArgumentException(sprintf(
                'The given credentials must implement the interface "%s"',
                'Affilicious\Shop\Domain\Model\Provider\Amazon\Amazon_Credentials_Interface'
            ));
        }

        parent::__construct($credentials);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function update_shop(Shop_Interface $shop)
    {
        // Nothing to do here yet
    }
}
