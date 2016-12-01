<?php
namespace Affilicious\Shop\Application\Validator;

use Affilicious\Shop\Domain\Exception\Missing_Credentials_Exception;
use Affilicious\Shop\Domain\Model\Provider\Credentials;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

interface Amazon_Credentials_Validator_Interface
{
    /**
     * Validate the amazon credentials.
     *
     * @since 0.7
     * @param Credentials $credentials
     * @throws Missing_Credentials_Exception
     */
    public function validate(Credentials $credentials);
}
