<?php
namespace Affilicious\ProductsPlugin\Product\Infrastructure\Persistence\Carbon;

use Affilicious\ProductsPlugin\Product\Domain\Model\DetailGroupFactory;
use Affilicious\ProductsPlugin\Product\Domain\Model\FieldGroup;
use Affilicious\ProductsPlugin\Product\Domain\Model\FieldGroupRepositoryInterface;
use Affilicious\ProductsPlugin\Product\Domain\Model\PriceComparison;
use Affilicious\ProductsPlugin\Product\Domain\Model\Product;
use Affilicious\ProductsPlugin\Product\Domain\Model\ProductRepositoryInterface;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class CarbonProductRepository implements ProductRepositoryInterface
{
    const PRICE_COMPARISON_DEFAULT_POSITION = 'affilicious_price_comparison_default_position';
    const PRICE_COMPARISON_EAN = 'affilicious_price_comparison_ean';
    const PRICE_COMPARISON_SHOPS = 'affilicious_price_comparison_shops';

    /**
     * @var FieldGroupRepositoryInterface
     */
    private $fieldGroupRepository;

    /**
     * @var DetailGroupFactory
     */
    private $detailGroupFactory;

    /**
     * CarbonProductRepository constructor.
     */
    public function __construct()
    {
        $this->fieldGroupRepository = new CarbonFieldGroupRepository();
        $this->detailGroupFactory = new DetailGroupFactory();
    }

    /**
     * @inheritdoc
     */
    public function findById($productId)
    {
        // The product ID is just a simple post ID, since the product is just a custom post type
        $post = get_post($productId);
        if ($post === null) {
            return null;
        }

        $product = self::fromPost($post);
        return $product;
    }

    /**
     * @inheritdoc
     */
    public function findAll()
    {
        $query = new \WP_Query(array(
            'post_type' => Product::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        $products = array();
        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $product = self::fromPost($query->post);
                $products[] = $product;
            }

            wp_reset_postdata();
        }

        return $products;
    }

    /**
     * Convert the Wordpress post into a product
     * @param \WP_Post $post
     * @return Product
     */
    private function fromPost(\WP_Post $post)
    {
        $product = new Product($post);

        // Setup the price comparison
        $defaultPosition = carbon_get_post_meta($post->ID, self::PRICE_COMPARISON_DEFAULT_POSITION);
        $priceComparison = new PriceComparison($defaultPosition);

        $ean = carbon_get_post_meta($post->ID, self::PRICE_COMPARISON_EAN);
        if (!empty($ean)) {
            $priceComparison->setEan($ean);
        }

        $shops = carbon_get_post_meta($post->ID, self::PRICE_COMPARISON_SHOPS, 'complex');
        if (!empty($shops)) {
            $priceComparison->setShops($shops);
        }

        $product->setPriceComparison($priceComparison);

        $query = new \WP_Query(array(
            'post_type' => FieldGroup::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();

                $fieldGroup = $this->fieldGroupRepository->findById($query->post->ID);
                $product->addFieldGroup($fieldGroup);

                $title = $fieldGroup->getTitle();
                if(!empty($title)) {
                    $detailGroup = $this->detailGroupFactory->create($product, $fieldGroup);
                    $product->addDetailGroup($detailGroup);
                }
            }

            wp_reset_postdata();
        }

        return $product;
    }
}
