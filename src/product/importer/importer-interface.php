<?php
namespace Affilicious\Product\Importer;

use Affilicious\Product\Model\Product;
use Affilicious\Shop\Model\Affiliate_Product_Id;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface Importer_Interface
{
    /**
     * Import the product from the provider by the ID and optional configuration.
     *
     * @since 0.9
     * @param Affiliate_Product_Id $affiliate_product_affiliateproduct_id The product ID like ASIN, Ebay ID and etc.
     * @param array $config Additional configuration options for the import.
     * @return Product|\WP_Error The imported simple product, complex product, product variant or any other type. It might be an error too.
     */
    public function import(Affiliate_Product_Id $affiliate_product_affiliateproduct_id, array $config = []);
}
