<?php
namespace Affilicious\Provider\Validator;

use Affilicious\Provider\Model\Credentials;

/**
 * @deprecated 1.2 Don't use it anymore
 */
interface Credentials_Validator_Interface
{
    /**
     * Validate the provider credentials.
     *
     * @deprecated 1.2 Don't use it anymore
     * @since 0.9
     * @param Credentials $credentials The credentials to validate.
     * @return bool|\WP_Error Either true or an error.
     */
    public function validate(Credentials $credentials);
}
