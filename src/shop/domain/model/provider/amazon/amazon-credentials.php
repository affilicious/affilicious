<?php
namespace Affilicious\Shop\Domain\Model\Provider\Amazon;

use Affilicious\Shop\Domain\Exception\Missing_Credentials_Exception;
use Affilicious\Shop\Domain\Model\Provider\Abstract_Credentials;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Amazon_Credentials extends Abstract_Credentials implements Amazon_Credentials_Interface
{
    const ACCESS_KEY_ID = 'access_key_id';
    const SECRET_ACCESS_KEY = 'secret_access_key';
    const COUNTRY = 'country';
    const PARTNER_TAG = 'partner_tag';

    /**
     * @var Access_Key_Id
     */
    private $access_key_id;

    /**
     * @var Secret_Access_Key
     */
    private $secret_access_key;

    /**
     * @var Country
     */
    private $country;

    /**
     * @var Partner_Tag
     */
    private $partner_tag;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function __construct($credentials)
    {
        $this->validate_credentials($credentials);

        parent::__construct($credentials);
        $this->access_key_id = new Access_Key_Id($credentials[self::ACCESS_KEY_ID]);
        $this->secret_access_key = new Secret_Access_Key($credentials[self::SECRET_ACCESS_KEY]);
        $this->country = new Country($credentials[self::COUNTRY]);
        $this->partner_tag = new Partner_Tag($credentials[self::PARTNER_TAG]);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_access_key_id()
    {
        return $this->access_key_id;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_secret_access_key()
    {
        return $this->secret_access_key;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_country()
    {
        return $this->country;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_partner_tag()
    {
        return $this->partner_tag;
    }

    /**
     * Validate the raw credentials for Amazon
     *
     * @since 0.7
     * @param $credentials
     * @throws Missing_Credentials_Exception
     */
    private function validate_credentials($credentials)
    {
        if(!key_exists(self::ACCESS_KEY_ID, $credentials)) {
            throw new Missing_Credentials_Exception(sprintf(
                'The access key ID for the Amazon provider is missing.'
            ));
        }

        if(!key_exists(self::SECRET_ACCESS_KEY, $credentials)) {
            throw new Missing_Credentials_Exception(sprintf(
                'The secret access key for the Amazon provider is missing.'
            ));
        }

        if(!key_exists(self::COUNTRY, $credentials)) {
            throw new Missing_Credentials_Exception(sprintf(
                'The country for the Amazon provider is missing.'
            ));
        }

        if(!key_exists(self::PARTNER_TAG, $credentials)) {
            throw new Missing_Credentials_Exception(sprintf(
                'The partner tag for the Amazon provider is missing.'
            ));
        }
    }
}
