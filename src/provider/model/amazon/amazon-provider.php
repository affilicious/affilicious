<?php
namespace Affilicious\Provider\Model\Amazon;

use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Provider\Model\Credentials;
use Affilicious\Provider\Model\Provider;
use Webmozart\Assert\Assert;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Amazon_Provider extends Provider
{
    const ACCESS_KEY = 'access_key';
    const SECRET_KEY = 'secret_key';
    const COUNTRY = 'country';
    const ASSOCIATE_TAG = 'associate_tag';

    /**
     * @var Access_Key
     */
    private $access_key;

    /**
     * @var Secret_Key
     */
    private $secret_key;

    /**
     * @var Country
     */
    private $country;

    /**
     * @var Associate_Tag
     */
    private $associate_tag;

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function __construct(Name $name, Slug $slug, Credentials $credentials)
    {
        $this->validate_credentials($credentials);

        parent::__construct($name, $slug, $credentials);
        $this->access_key = new Access_Key($credentials->get(self::ACCESS_KEY));
        $this->secret_key = new Secret_Key($credentials->get(self::SECRET_KEY));
        $this->country = new Country($credentials->get(self::COUNTRY));
        $this->associate_tag = new Associate_Tag($credentials->get(self::ASSOCIATE_TAG));
    }

    /**
     * Get the Amazon access key id from the credentials.
     *
     * @since 0.8
     * @return Access_Key
     */
    public function get_access_key()
    {
        return $this->access_key;
    }

    /**
     * Get the Amazon secret access key from the credentials.
     *
     * @since 0.8
     * @return Secret_Key
     */
    public function get_secret_key()
    {
        return $this->secret_key;
    }

    /**
     * Get the Amazon country code from the credentials.
     *
     * @since 0.8
     * @return Country
     */
    public function get_country()
    {
        return $this->country;
    }

    /**
     * Get the Amazon partner tag from the credentials.
     *
     * @since 0.8
     * @return Associate_Tag
     */
    public function get_associate_tag()
    {
        return $this->associate_tag;
    }

    /**
     * Validate the credentials for Amazon.
     *
     * @since 0.8
     * @param Credentials $credentials
     */
    protected function validate_credentials(Credentials $credentials)
    {
        $value = $credentials->get_value();

        Assert::keyExists($value, self::ACCESS_KEY, 'The access key ID for the Amazon provider is missing.');
        Assert::keyExists($value, self::SECRET_KEY, 'The secret access key for the Amazon provider is missing.');
        Assert::keyExists($value, self::COUNTRY, 'The country for the Amazon provider is missing.');
        Assert::keyExists($value, self::ASSOCIATE_TAG, 'The associate tag for the Amazon provider is missing.');
    }
}
