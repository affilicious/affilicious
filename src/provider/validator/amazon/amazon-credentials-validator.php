<?php
namespace Affilicious\Provider\Validator\Amazon;

use Affilicious\Provider\Model\Amazon\Amazon_Provider;
use Affilicious\Provider\Model\Credentials;
use Affilicious\Provider\Validator\Credentials_Validator_Interface;
use ApaiIO\ApaiIO;
use ApaiIO\Configuration\GenericConfiguration;
use ApaiIO\Operations\Search;
use ApaiIO\Request\GuzzleRequest;
use GuzzleHttp\Client;

class Amazon_Credentials_Validator implements Credentials_Validator_Interface
{
    /**
     * @inheritdoc
     * @since 0.8
     */
    public function validate(Credentials $credentials)
    {
        $access_key = $credentials->get(Amazon_Provider::ACCESS_KEY);
        if(empty($access_key)) {
        	return new \WP_Error('aff_provider_validator_amazon_missing_access_key', __('The access key ID for the Amazon provider is missing.', 'affilicious'));
        }

        $secret_key = $credentials->get(Amazon_Provider::SECRET_KEY);
	    if(empty($secret_key)) {
		    return new \WP_Error('aff_provider_validator_amazon_missing_secret_key', __('The secret access key for the Amazon provider is missing.', 'affilicious'));
	    }

        $country = $credentials->get(Amazon_Provider::COUNTRY);
	    if(empty($country)) {
		    return new \WP_Error('aff_provider_validator_amazon_missing_country', __('The country for the Amazon provider is missing.', 'affilicious'));
	    }

        $associate_tag = $credentials->get(Amazon_Provider::ASSOCIATE_TAG);
	    if(empty($associate_tag)) {
		    return new \WP_Error('aff_provider_validator_amazon_missing_associate_tag', __('The associate tag for the Amazon provider is missing.', 'affilicious'));
	    }

        try {
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
            if($e->getCode() != 503) {
                return new \WP_Error('aff_provider_validator_amazon_error', $e->getMessage());
            }
        }

        return true;
    }
}
