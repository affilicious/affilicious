<?php
namespace Affilicious\Product\Search;

use Affilicious\Product\Model\Product;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @deprecated 1.2 Don't use it anymore
 * @since 0.9
 */
interface Search_Interface
{
    /**
     * Search the products by making an provider API call.
     *
     * @deprecated 1.2 Don't use it anymore
     * @since 0.9
     * @param array $params Configuration options for the search
     * @return Product[]|\WP_Error The found simple product, complex product, product variant or any other type. It might be an error too.
     */
    public function search(array $params);
}
