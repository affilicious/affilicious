<?php
namespace Affilicious\Provider\Validator;

use Affilicious\Provider\Model\Credentials;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

interface Amazon_Credentials_Validator_Interface
{
    /**
     * Validate the Amazon credentials.
     *
     * @since 0.8
     * @param Credentials $credentials
     * @return bool
     */
    public function validate(Credentials $credentials);
}
