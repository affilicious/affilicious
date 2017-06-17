<?php
namespace Affilicious\Product\Migration;

use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Repository\Product_Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

final class Product_Variant_Terms_To_0820_Migration
{
    const OPTION = 'aff_migrated_product_variant_terms_to_0.8.20';

    /**
     * @var Product_Repository_Interface
     */
    private $product_repository;

    /**
     * @since 0.8.20
     * @param Product_Repository_Interface $product_repository
     */
    public function __construct(Product_Repository_Interface $product_repository)
    {
        $this->product_repository = $product_repository;
    }

    /**
     * Restore the complex parent products to refresh the product variant taxonomy terms.
     *
     * @since 0.8.20
     */
    public function migrate()
    {
        if(\Affilicious::VERSION >= '0.8.19' && get_option(self::OPTION) != 'yes') {

            $products = $this->product_repository->find_all();
            foreach ($products as $product) {
                if($product instanceof Complex_Product) {
                    $this->product_repository->store($product);
                }
            }

            update_option(self::OPTION, 'yes');
        }
    }
}
