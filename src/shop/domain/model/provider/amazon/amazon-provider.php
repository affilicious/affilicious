<?php
namespace Affilicious\Shop\Domain\Model\Provider\Amazon;

use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Shop\Domain\Exception\Missing_Credentials_Exception;
use Affilicious\Shop\Domain\Model\Provider\Abstract_Provider;
use Affilicious\Shop\Domain\Model\Provider\Credentials;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Amazon_Provider extends Abstract_Provider implements Amazon_Provider_Interface
{
    const CREDENTIALS_ACCESS_KEY_ID = 'access_key_id';
    const CREDENTIALS_SECRET_ACCESS_KEY = 'secret_access_key';
    const CREDENTIALS_COUNTRY = 'country';
    const CREDENTIALS_PARTNER_TAG = 'partner_tag';

    /**
     * @var Access_Key_Id
     */
    protected $access_key_id;

    /**
     * @var Secret_Access_Key
     */
    protected $secret_access_key;

    /**
     * @var Country
     */
    protected $country;

    /**
     * @var Partner_Tag
     */
    protected $partner_tag;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function __construct(Title $title, Name $name, Key $key, Credentials $credentials)
    {
        $this->validate_credentials($credentials);

        parent::__construct($title, $name, $key, $credentials);

        $raw_credentials = $credentials->get_value();
        $this->access_key_id = new Access_Key_Id($raw_credentials[self::CREDENTIALS_ACCESS_KEY_ID]);
        $this->secret_access_key = new Secret_Access_Key($raw_credentials[self::CREDENTIALS_SECRET_ACCESS_KEY]);
        $this->country = new Country($raw_credentials[self::CREDENTIALS_COUNTRY]);
        $this->partner_tag = new Partner_Tag($raw_credentials[self::CREDENTIALS_PARTNER_TAG]);
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
     * Validate the credentials for Amazon.
     *
     * @since 0.7
     * @param Credentials $credentials
     * @throws Missing_Credentials_Exception
     */
    protected function validate_credentials(Credentials $credentials)
    {
        $value = $credentials->get_value();

        if(!key_exists(self::CREDENTIALS_ACCESS_KEY_ID, $value)) {
            throw new Missing_Credentials_Exception(sprintf(
                'The access key ID for the Amazon provider is missing.'
            ));
        }

        if(!key_exists(self::CREDENTIALS_SECRET_ACCESS_KEY, $value)) {
            throw new Missing_Credentials_Exception(sprintf(
                'The secret access key for the Amazon provider is missing.'
            ));
        }

        if(!key_exists(self::CREDENTIALS_COUNTRY, $value)) {
            throw new Missing_Credentials_Exception(sprintf(
                'The country for the Amazon provider is missing.'
            ));
        }

        if(!key_exists(self::CREDENTIALS_PARTNER_TAG, $value)) {
            throw new Missing_Credentials_Exception(sprintf(
                'The partner tag for the Amazon provider is missing.'
            ));
        }
    }
}
