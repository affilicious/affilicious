<?php
namespace Affilicious\Shop\Application\Validator;

use Affilicious\Shop\Domain\Exception\Missing_Credentials_Exception;
use Affilicious\Shop\Domain\Model\Provider\Amazon\Amazon_Provider_Interface;
use Affilicious\Shop\Domain\Model\Provider\Credentials;
use ApaiIO\ApaiIO;
use ApaiIO\Configuration\GenericConfiguration;
use ApaiIO\Operations\Search;
use GuzzleHttp\Client;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;
use ApaiIO\Request\GuzzleRequest;

class Amazon_Credentials_Validator implements Amazon_Credentials_Validator_Interface
{
    /**
     * @inheritdoc
     * @since 0.7
     */
    public function validate(Credentials $credentials)
    {
        $access_key = $credentials->get(Amazon_Provider_Interface::ACCESS_KEY);
        $secret_key = $credentials->get(Amazon_Provider_Interface::SECRET_KEY);
        $country = $credentials->get(Amazon_Provider_Interface::COUNTRY);
        $associate_tag = $credentials->get(Amazon_Provider_Interface::ASSOCIATE_TAG);

        if($access_key === null) {
            throw new Missing_Credentials_Exception(sprintf(
                'The access key ID for the Amazon provider is missing.'
            ));
        }

        if($secret_key === null) {
            throw new Missing_Credentials_Exception(sprintf(
                'The secret access key for the Amazon provider is missing.'
            ));
        }

        if($country === null) {
            throw new Missing_Credentials_Exception(sprintf(
                'The country for the Amazon provider is missing.'
            ));
        }

        if($associate_tag === null) {
            throw new Missing_Credentials_Exception(sprintf(
                'The associate tag for the Amazon provider is missing.'
            ));
        }

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
    }
}
