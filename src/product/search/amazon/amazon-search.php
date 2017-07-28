<?php
namespace Affilicious\Product\Search\Amazon;

use Affilicious\Common\Model\Slug;
use Affilicious\Product\Helper\Amazon_Helper;
use Affilicious\Product\Search\Search_Interface;
use Affilicious\Provider\Model\Amazon\Amazon_Provider;
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
    const CATEGORY_ALL = 'All';
    const CATEGORY_APPAREL = 'Apparel';
    const CATEGORY_APPLIANCES = 'Appliances';
    const CATEGORY_AUTOMOTIVE = 'Automotive';
    const CATEGORY_BABY = 'Baby';
    const CATEGORY_BEAUTY = 'Beauty';
    const CATEGORY_BOOKS = 'Books';
    const CATEGORY_DVD = 'DVD';
    const CATEGORY_ELECTRONICS = 'Electronics';
    const CATEGORY_FURNITURE = 'Furniture';
    const CATEGORY_GIFT_CARDS = 'GiftCards';
    const CATEGORY_GROCERY = 'Grocery';
    const CATEGORY_HEALTH_PERSONAL_CARE = 'HealthPersonalCare';
    const CATEGORY_HOME_GARDEN = 'HomeGarden';
    const CATEGORY_INDUSTRIAL = 'Industrial';
    const CATEGORY_JEWELRY = 'Jewelry';
    const CATEGORY_KINDLE_STORE = 'KindleStore';
    const CATEGORY_LAWN_AND_GARDEN = 'LawnAndGarden';
    const CATEGORY_LUGGAGE = 'Luggage';
    const CATEGORY_LUXURY_BEAUTY = 'LuxuryBeauty';
    const CATEGORY_MARKETPLACE = 'Marketplace';
    const CATEGORY_MUSIC = 'Music';
    const CATEGORY_MUSICAL_INSTRUMENTS = 'MusicalInstruments';
    const CATEGORY_OFFICE_PRODUCTS = 'OfficeProducts';
    const CATEGORY_PANTRY = 'Pantry';
    const CATEGORY_PC_HARDWARE = 'PCHardware';
    const CATEGORY_PET_SUPPLIES = 'PetSupplies';
    const CATEGORY_SHOES = 'Shoes';
    const CATEGORY_SOFTWARE = 'Software';
    const CATEGORY_SPORTING_GOODS = 'SportingGoods';
    const CATEGORY_TOYS = 'Toys';
    const CATEGORY_VIDEO_GAMES = 'VideoGames';
    const CATEGORY_WATCHES = 'Watches';

    public static $categories = [
        self::CATEGORY_ALL,
        self::CATEGORY_APPAREL,
        self::CATEGORY_APPLIANCES,
        self::CATEGORY_AUTOMOTIVE,
        self::CATEGORY_BABY,
        self::CATEGORY_BEAUTY,
        self::CATEGORY_BOOKS,
        self::CATEGORY_DVD,
        self::CATEGORY_ELECTRONICS,
        self::CATEGORY_FURNITURE,
        self::CATEGORY_GIFT_CARDS,
        self::CATEGORY_GROCERY,
        self::CATEGORY_HEALTH_PERSONAL_CARE,
        self::CATEGORY_HOME_GARDEN,
        self::CATEGORY_INDUSTRIAL,
        self::CATEGORY_JEWELRY,
        self::CATEGORY_KINDLE_STORE,
        self::CATEGORY_LAWN_AND_GARDEN,
        self::CATEGORY_LUGGAGE,
        self::CATEGORY_LUXURY_BEAUTY,
        self::CATEGORY_MARKETPLACE,
        self::CATEGORY_MUSIC,
        self::CATEGORY_MUSICAL_INSTRUMENTS,
        self::CATEGORY_OFFICE_PRODUCTS,
        self::CATEGORY_PANTRY,
        self::CATEGORY_PC_HARDWARE,
        self::CATEGORY_PET_SUPPLIES,
        self::CATEGORY_SHOES,
        self::CATEGORY_SOFTWARE,
        self::CATEGORY_SPORTING_GOODS,
        self::CATEGORY_TOYS,
        self::CATEGORY_VIDEO_GAMES,
        self::CATEGORY_WATCHES,
    ];

    /**
     * @var Provider_Repository_Interface
     */
    protected $provider_repository;

    /**
     * @since 0.9
     * @param Provider_Repository_Interface $provider_repository
     */
    public function __construct(Provider_Repository_Interface $provider_repository)
    {
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

        return $products;
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
            return new \WP_Error('aff_amazon_search_missing_term', __('The Amazon search term is missing', 'affilicious'));
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
            return new \WP_Error('aff_amazon_search_missing_category', __('The Amazon search category is missing', 'affilicious'));
        }

        if(!in_array($category, self::$categories)) {
            return new \WP_Error('aff_amazon_search_invalid_category', sprintf(
                'The Amazon search category is not valid. Choose from: %s',
                implode(', ', self::$categories)
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

        $with_variants = $this->find_with_variants($params);

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

        $operation = $this->create_search_operation($term, $type, $category, $with_variants, $page);

        try {
            $apaiIO = new ApaiIO($conf);
            $response = $apaiIO->runOperation($operation);
        } catch (\Exception $e) {
            $response = new \WP_Error('aff_product_amazon_search_error', $e->getMessage());
        }

        if(isset($response['Items']['Request']['Errors']['Error'])) {
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
     * @param string $term
     * @param string $type
     * @param string $category
     * @param bool $with_variants
     * @param int $page
     * @return OperationInterface
     */
    protected function create_search_operation($term, $type, $category, $with_variants, $page)
    {
        $response_group = ['Small', 'Images', 'Offers', 'ItemAttributes'];
        if($with_variants) {
            $response_group = array_merge($response_group, ['VariationMatrix', 'VariationOffers']);
        }

        if($type == 'keywords') {
            $operation = new Search();
            $operation->setKeywords($term);
            $operation->setCategory($category);
            $operation->setPage($page);
        } else {
            $operation = new Lookup();
            $operation->setItemId($term);
        }

        $operation->setResponseGroup($response_group);

        return $operation;
    }
}
