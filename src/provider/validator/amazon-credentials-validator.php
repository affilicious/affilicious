<?php
namespace Affilicious\Provider\Validator;

use Affilicious\Provider\Model\Amazon\Amazon_Provider;
use Affilicious\Provider\Model\Credentials;
use ApaiIO\ApaiIO;
use ApaiIO\Configuration\GenericConfiguration;
use ApaiIO\Operations\Search;
use ApaiIO\Request\GuzzleRequest;
use GuzzleHttp\Client;
use Webmozart\Assert\Assert;

class Amazon_Credentials_Validator implements Credentials_Validator_Interface
{
    /**
     * @inheritdoc
     * @since 0.8
     */
    public function validate(Credentials $credentials)
    {
        $access_key = $credentials->get(Amazon_Provider::ACCESS_KEY);
        $secret_key = $credentials->get(Amazon_Provider::SECRET_KEY);
        $country = $credentials->get(Amazon_Provider::COUNTRY);
        $associate_tag = $credentials->get(Amazon_Provider::ASSOCIATE_TAG);

        try {
            Assert::notNull($access_key, 'The access key ID for the Amazon provider is missing.');
            Assert::notNull($secret_key, 'The secret access key for the Amazon provider is missing.');
            Assert::notNull($country, 'The country for the Amazon provider is missing.');
            Assert::notNull($associate_tag, 'The associate tag for the Amazon provider is missing.');

            $conf = new GenericConfiguration();
            $client = new Client();
            $request = new GuzzleRequest($client);

            $conf
                ->setAccessKey($access_key)
                ->setSecretKey($secret_key)
                ->setCountry($country)
                ->setAssociateTag($associate_tag)
                ->setRequest($request);

            $apaiIO = new ApaiIO($conf);
            $search = new Search();
            $search->setKeywords('Affilicious');

            $apaiIO->runOperation($search);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
