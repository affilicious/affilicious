<?php
namespace Affilicious\Provider\Validator;

use Affilicious\Provider\Model\Credentials;

interface Credentials_Validator_Interface
{
    /**
     * Validate the provider credentials.
     *
     * @since 0.9
     * @param Credentials $credentials
     * @return bool
     */
    public function validate(Credentials $credentials);
}
