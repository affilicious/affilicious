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
    /**
     * @var Access_Key
     */
    protected $access_key;

    /**
     * @var Secret_Key
     */
    protected $secret_key;

    /**
     * @var Country
     */
    protected $country;

    /**
     * @var Associate_Tag
     */
    protected $associate_tag;

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function __construct(Title $title, Name $name, Key $key, Credentials $credentials)
    {
        $this->validate_credentials($credentials);

        parent::__construct($title, $name, $key, $credentials);

        $raw_credentials = $credentials->get_value();
        $this->access_key = new Access_Key($raw_credentials[self::ACCESS_KEY]);
        $this->secret_key = new Secret_Key($raw_credentials[self::SECRET_KEY]);
        $this->country = new Country($raw_credentials[self::COUNTRY]);
        $this->associate_tag = new Associate_Tag($raw_credentials[self::ASSOCIATE_TAG]);
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_access_key()
    {
        return $this->access_key;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function get_secret_key()
    {
        return $this->secret_key;
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
    public function get_associate_tag()
    {
        return $this->associate_tag;
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

        if(!key_exists(self::ACCESS_KEY, $value)) {
            throw new Missing_Credentials_Exception(sprintf(
                'The access key ID for the Amazon provider is missing.'
            ));
        }

        if(!key_exists(self::SECRET_KEY, $value)) {
            throw new Missing_Credentials_Exception(sprintf(
                'The secret access key for the Amazon provider is missing.'
            ));
        }

        if(!key_exists(self::COUNTRY, $value)) {
            throw new Missing_Credentials_Exception(sprintf(
                'The country for the Amazon provider is missing.'
            ));
        }

        if(!key_exists(self::ASSOCIATE_TAG, $value)) {
            throw new Missing_Credentials_Exception(sprintf(
                'The associate tag for the Amazon provider is missing.'
            ));
        }
    }
}
