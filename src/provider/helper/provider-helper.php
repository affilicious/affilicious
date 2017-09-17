<?php
namespace Affilicious\Provider\Helper;

use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Provider\Model\Credentials;
use Affilicious\Provider\Model\Provider;
use Affilicious\Provider\Model\Provider_Id;
use Affilicious\Provider\Model\Type;
use Affilicious\Provider\Repository\Provider_Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Provider_Helper
{
    /**
     * Check if the Wordpress term or term ID belongs to a provider template.
     *
     * @since 0.8.9
     * @param int|string|array|\WP_Term|Provider $id
     * @return bool
     */
    public static function is_provider($id)
    {
        /** @var Provider_Repository_Interface $provider_repository */
        $provider_repository = \Affilicious::get('affilicious.provider.repository.provider');

        // The argument is already a provider template
        if ($id instanceof Provider) {
            return true;
        }

        // The argument is a provider template ID
        if($id instanceof Provider_Id) {
            return null !== $provider_repository->find_one_by_id($id);
        }

        // The argument is an integer or string.
        if(is_int($id) || is_string($id)) {
            return null !== $provider_repository->find_one_by_id(new Provider_Id($id));
        }

        // The argument is an array.
        if(is_array($id) && !empty($id['id'])) {
            return null !== $provider_repository->find_one_by_id(new Provider_Id($id['id']));
        }

        return null;
    }

    /**
     * Find one provider template by the ID or Wordpress term.
     *
     * @since 0.8.9
     * @param int|string|array|Provider|Provider_Id $id
     * @return Provider|null
     */
    public static function get_provider($id)
    {
        /** @var Provider_Repository_Interface $provider_repository */
        $provider_repository = \Affilicious::get('affilicious.provider.repository.provider');

        // The argument is already an provider template
        if ($id instanceof Provider) {
            return $id;
        }

        // The argument is a provider template ID
        if($id instanceof Provider_Id) {
            return $provider_repository->find_one_by_id($id);
        }

        // The argument is an integer or string.
        if(is_int($id) || is_string($id)) {
            return $provider_repository->find_one_by_id(new Provider_Id($id));
        }

        // The argument is an array.
        if(is_array($id) && !empty($id['id'])) {
            return $provider_repository->find_one_by_id(new Provider_Id($id['id']));
        }

        return null;
    }

    /**
     * Convert the provider into an array.
     *
     * @since 0.8.9
     * @param Provider $provider
     * @return array
     */
    public static function to_array(Provider $provider)
    {
        $array = array(
            'id' => $provider->has_id() ? $provider->get_id()->get_value() : null,
            'name' => $provider->get_name()->get_value(),
            'slug' => $provider->get_slug()->get_value(),
            'credentials' => $provider->get_credentials()->get_value(),
            'type' => $provider->get_type() !== null ? $provider->get_type()->get_value() : null,
	        'custom_values' => $provider->has_custom_values() ? $provider->get_custom_values() : null,
        );

        $array = apply_filters('aff_provider_to_array', $array, $provider);

        return $array;
    }

	/**
	 * Convert the array into an provider.
	 *
	 * @since 0.9.7
	 * @param array| $array
	 * @return Provider null
	 */
    public static function from_array(array $array)
    {
    	$id = !empty($array['id']) ? $array['id'] : null;
    	$name = !empty($array['name']) ? $array['name'] : null;
    	$slug = !empty($array['slug']) ? $array['slug'] : null;
	    $credentials = !empty($array['credentials']) ? $array['credentials'] : null;
	    $type = !empty($array['type']) ? $array['type'] : null;
	    $custom_values = !empty($array['custom_values']) ? $array['custom_values'] : null;

    	if(empty($name) || empty($slug) || empty($credentials)) {
    		return null;
	    }

	    $provider_class = apply_filters('aff_array_to_provider_class', Provider::class, $array);

    	/** @var Provider $provider */
	    $provider = new $provider_class(
	    	new Name($name),
		    new Slug($slug),
		    new Credentials($credentials),
		    !empty($type) ? new Type($type) : null // Required in future versions.
	    );

    	if(!empty($id)) {
    		$provider->set_id(new Provider_Id($id));
	    }

	    if(!empty($custom_values)) {
    		$provider->set_custom_values($custom_values);
	    }

	    $provider = apply_filters('aff_array_to_provider', $provider, $array);

    	return $provider;
    }
}
