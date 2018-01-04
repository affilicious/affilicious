<?php
namespace Affilicious\Product\Search\Amazon;

use Affilicious\Common\Model\Custom_Value_Aware_Interface;
use Affilicious\Common\Model\Slug;
use Affilicious\Product\Helper\Amazon_Helper;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Shop_Aware_Interface;
use Affilicious\Product\Repository\Product_Repository_Interface;
use Affilicious\Product\Search\Search_Interface;
use Affilicious\Provider\Model\Amazon\Amazon_Provider;
use Affilicious\Provider\Model\Amazon\Category;
use Affilicious\Provider\Repository\Provider_Repository_Interface;
use ApaiIO\ApaiIO;
use ApaiIO\Configuration\GenericConfiguration;
use ApaiIO\Operations\Lookup;
use ApaiIO\Operations\OperationInterface;
use ApaiIO\Operations\Search;
use ApaiIO\Request\GuzzleRequest;
use ApaiIO\ResponseTransformer\XmlToArray;
use GuzzleHttp\Client;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @see http://docs.aws.amazon.com/AWSECommerceService/latest/DG/LocaleIN.html
 */
class Amazon_Search implements Search_Interface
{
	/**
	 * @var Product_Repository_Interface
	 */
	protected $product_repository;

    /**
     * @var Provider_Repository_Interface
     */
    protected $provider_repository;

	/**
	 * @since 0.9
	 * @param Product_Repository_Interface $product_repository
	 * @param Provider_Repository_Interface $provider_repository
	 */
    public function __construct(Product_Repository_Interface $product_repository, Provider_Repository_Interface $provider_repository)
    {
	    $this->product_repository = $product_repository;
	    $this->provider_repository = $provider_repository;
    }

    /**
     * @inheritdoc
     * @since 0.9
     */
    public function search(array $params)
    {
        $response = $this->request($params);
        if($response instanceof \WP_Error) {
            if(in_array('aff_product_amazon_search_no_results', $response->get_error_codes())) {
                return [];
            }

            return $response;
        }

        if(isset($response['Items']['TotalResults']) && intval($response['Items']['TotalResults']) == 0) {
            return [];
        }

        $results = isset($response['Items']['Item'][0]) ? $response['Items']['Item'] : [$response['Items']['Item']] ;
        $products = array_map(function (array $result) use ($params) {
            return Amazon_Helper::create_product($result, [
                'variants' => !empty($params['with_variants']),
                'store_thumbnail' => false,
                'store_image_gallery' => false,
                'store_shop' => false,
                'store_attributes' => false,
            ]);
        }, $results);

        // Mark the products as already imported if it's true.
	    foreach($products as $product) {
		    $this->add_already_imported($product);
        }

        return $products;
    }

	/**
	 * Check if the product is already imported.
	 *
	 * @since 0.9.15
	 * @param Product $product
	 * @return bool
	 */
    protected function add_already_imported(Product $product)
    {
	    $already_imported  = false;

	    if($product instanceof Shop_Aware_Interface && $product instanceof Custom_Value_Aware_Interface) {
		    $shops = $product->get_shops();
		    $shop = !empty($shops[0]) ? $shops[0] : null;
		    if($shop !== null) {
			    $products_with_shop = $this->product_repository->find_all([
				    'post_parent' => 0,
				    'meta_query'  => [
					    [
						    'key'   => "_affilicious_product_shops_%-_affiliate_product_id_%",
						    'value' => $shop->get_tracking()->get_affiliate_product_id()->get_value(),
					    ],
				    ],
			    ]);

			    $already_imported = !empty($products_with_shop);
		    }
	    }

	    $product->add_custom_value('already_imported', $already_imported);

	    return $already_imported;
    }

    /**
     * Find the search term in the parameters.
     *
     * @since 0.9
     * @param array $params The parameters for the Amazon search.
     * @return string|\WP_Error Either the term or an error.
     */
    protected function find_term(array $params)
    {
        $term = isset($params['term']) ? $params['term'] : null;
        if(empty($term)) {
            return new \WP_Error('aff_amazon_search_missing_term', __('The Amazon search term is missing.', 'affilicious'));
        }

        return $term;
    }

    /**
     * Find the search type in the parameters.
     *
     * @since 0.9
     * @param array $params The parameters for the Amazon search.
     * @return string|\WP_Error Either the type or an error.
     */
    protected function find_type(array $params)
    {
        $type = isset($params['type']) ? $params['type'] : null;
        if(empty($type)) {
            return new \WP_Error('aff_amazon_search_missing_type', __('The Amazon search type is missing', 'affilicious'));
        }

        return $type;
    }

    /**
     * Find the search category in the parameters.
     *
     * @since 0.9
     * @param array $params The parameters for the Amazon search.
     * @return string|\WP_Error Either the category or an error.
     */
    protected function find_category(array $params)
    {
        $category = isset($params['category']) ? $params['category'] : null;
        if(empty($category)) {
            return new \WP_Error('aff_amazon_search_missing_category', __('The Amazon search category is missing.', 'affilicious'));
        }

        if(!in_array($category, array_keys(Category::$germany))) {
            return new \WP_Error('aff_amazon_search_invalid_category', sprintf(
                'The Amazon search category %s is not valid. Choose from: %s',
                $category,
                implode(', ', Category::$germany)
            ));
        }

        return $category;
    }

    /**
     * Find out whether the search is performed with variants or not.
     *
     * @since 0.9
     * @param array $params The parameters for the Amazon search.
     * @return bool Whether the search is performed with variants or not.
     */
    protected function find_with_variants(array $params)
    {
        return !empty($params['with_variants']);
    }

	/**
	 * Find the min price for the search.
	 *
	 * @since 0.9.15
	 * @param array $params The parameters for the Amazon search.
	 * @return string|null|\WP_Error Either the min price or and error.
	 */
	protected function find_min_price(array $params)
	{
		$min_price = !empty($params['min_price']) && is_numeric($params['min_price']) ? $params['min_price'] : null;
		if($min_price === null) {
			return null;
		}

		if(!is_numeric($min_price)) {
			return new \WP_Error('aff_amazon_search_invalid_min_price', __('The Amazon search min price is invalid.', 'affilicious'));
		}

		$min_price = ceil(floatval($min_price) * 100);

		return $min_price;
	}

	/**
	 * Find the max price for the search.
	 *
	 * @since 0.9.15
	 * @param array $params The parameters for the Amazon search.
	 * @return string|null|\WP_Error Either the max price or and error.
	 */
	protected function find_max_price(array $params)
	{
		$max_price = !empty($params['max_price']) || is_numeric($params['max_price']) ? $params['max_price'] : null;
		if($max_price === null) {
			return null;
		}

		if(!is_numeric($max_price)) {
			return new \WP_Error('aff_amazon_search_invalid_max_price', __('The Amazon search max price is invalid.', 'affilicious'));
		}

		$max_price = ceil(floatval($max_price) * 100);

		return $max_price;
	}

	/**
	 * Find the condition for the search.
	 *
	 * @since 0.9.15
	 * @param array $params The parameters for the Amazon search.
	 * @return string The condition like all, new or used.
	 */
	protected function find_condition(array $params)
	{
		$condition = !empty($params['condition']) ? $params['condition'] : 'New';

		return $condition;
	}

	/**
	 * Find the sort order for the search.
	 *
	 * @since 0.9.15
	 * @param array $params The parameters for the Amazon search.
	 * @return string The sort order.
	 */
	protected function find_sort(array $params)
	{
		$sort = !empty($params['sort']) ? $params['sort'] : 'price-desc-rank';

		return $sort;
	}

    /**
     * Find the search page for the pagination.
     *
     * @since 0.9
     * @param array $params The parameters for the Amazon search.
     * @return int|\WP_Error The search page or an error.
     */
    protected function find_page(array $params)
    {
        $page = isset($params['page']) ? $params['page'] : 1;
        if($page > 5 || $page < 0) {
            return new \WP_Error('aff_amazon_search_invalid_page_range', 'The page for the Amazon search has to be between 0 and 5.');
        }

        return $page;
    }

    /**
     * Find the amazon provider for the search.
     *
     * @since 0.9
     * @return Amazon_Provider|\WP_Error Either the provider.
     */
    protected function find_provider()
    {
        $amazon_provider = $this->provider_repository->find_one_by_slug(new Slug('amazon'));
        if(!($amazon_provider instanceof Amazon_Provider)) {
            return new \WP_Error('aff_amazon_search_missing_provider', "The Amazon provider with the slug 'amazon' hasn't been found.");
        }

        return $amazon_provider;
    }

    /**
     * Lookup the Amazon product by the product ID.
     *
     * @since 0.9
     * @param array $params
     * @return array|\WP_Error
     */
    protected function request($params)
    {
        $amazon_provider = $this->find_provider();
        if($amazon_provider instanceof \WP_Error) {
            return $amazon_provider;
        }

        $term = $this->find_term($params);
        if($term instanceof \WP_Error) {
            return $term;
        }

        $type = $this->find_type($params);
        if($type instanceof \WP_Error) {
            return $type;
        }

        $category = $this->find_category($params);
        if($category instanceof \WP_Error) {
            return $category;
        }

        $page = $this->find_page($params);
        if($page instanceof \WP_Error) {
            return $page;
        }

        $min_price = $this->find_min_price($params);
	    if($min_price instanceof \WP_Error) {
		    return $min_price;
	    }

        $max_price = $this->find_max_price($params);
	    if($max_price instanceof \WP_Error) {
		    return $max_price;
	    }

	    $sort = $this->find_sort($params);
	    if($sort instanceof \WP_Error) {
		    return $sort;
	    }

        $condition = $this->find_condition($params);
	    if($condition instanceof \WP_Error) {
		    return $condition;
	    }

        $with_variants = $this->find_with_variants($params);

        // Prepare the search request.
        $conf = new GenericConfiguration();
        $client = new Client();
        $request = new GuzzleRequest($client);

        $conf
            ->setCountry($amazon_provider->get_country()->get_value())
            ->setAccessKey($amazon_provider->get_access_key()->get_value())
            ->setSecretKey($amazon_provider->get_secret_key()->get_value())
            ->setAssociateTag($amazon_provider->get_associate_tag()->get_value())
            ->setRequest($request)
            ->setResponseTransformer(new XmlToArray());

        $operation = $this->create_search_operation($term, $type, $category, $min_price, $max_price, $sort, $condition, $with_variants, $page);

        // Make the search request.
        try {
            $apaiIO = new ApaiIO($conf);
            $response = $apaiIO->runOperation($operation);
        } catch (\Exception $e) {
	        if($e->getCode() == 503) {
		        $response = new \WP_Error('aff_product_amazon_request_throttled', __('Amazon has throttled your request speed for a short time.', 'affilicious'));
	        } else {
		        $response = new \WP_Error('aff_product_amazon_search_error', $e->getMessage());
	        }
        }

        // Make the search request again for the parent item, if the search request type is "asin" and "with parents" is enabled.
	    if($type == 'asin' && $with_variants && isset($response['Items']['Item']['ParentASIN']) && $response['Items']['Item']['ASIN'] !== $response['Items']['Item']['ParentASIN']) {
		    $response = $this->request(wp_parse_args([
			    'term' => $response['Items']['Item']['ParentASIN']
		    ], $params));
	    }

	    // Check if there are any errors.
        if(!($response instanceof \WP_Error) && isset($response['Items']['Request']['Errors']['Error'])) {
            $errors = isset($response['Items']['Request']['Errors']['Error'][0]) ? $response['Items']['Request']['Errors']['Error'] : [$response['Items']['Request']['Errors']['Error']];
            $response = new \WP_Error();
            foreach ($errors as $error) {
                if($error['Code'] == 'AWS.ECommerceService.NoExactMatches') {
                    $response->add('aff_product_amazon_search_no_results', $error['Code'], $error['Message']);
                }

                $response->add('aff_product_amazon_search_error', $error['Code'], $error['Message']);
            }
        }

        return $response;
    }

	/**
	 * Create the search operation for the Amazon search based on the search parameters.
	 *
	 * @since 0.9
	 *
	 * @param string $term
	 * @param string $type
	 * @param string $category
	 * @param int|null $min_price
	 * @param int|null $max_price
	 * @param string|null $sort
	 * @param string $condition
	 * @param bool $with_variants
	 * @param int $page
	 * @return OperationInterface
	 */
    protected function create_search_operation($term, $type, $category, $min_price, $max_price, $sort, $condition, $with_variants, $page)
    {
        $response_group = ['Small', 'Images', 'Offers', 'ItemAttributes'];
        if($with_variants) {
            $response_group = array_merge($response_group, ['VariationMatrix', 'VariationOffers']);
        }

        if($type == 'keywords') {
            $operation = new Search();
            $operation->setKeywords($term);
            $operation->setPage($page);
            $operation->setCategory($category);
            $operation->setCondition($condition);
            $operation->setAvailability('Available');

	        if($category !== 'All' && $sort !== null) {
		        $operation->setSort($sort);
	        }

            if($min_price !== null) {
	            $operation->setMinimumPrice($min_price);
            }

            if($max_price !== null) {
	            $operation->setMaximumPrice($max_price);
            }
        } else {
            $operation = new Lookup();
            $operation->setItemId($term);
        }

        $operation->setResponseGroup($response_group);

        return $operation;
    }
}
