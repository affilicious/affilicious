<?php
namespace Affilicious\Product\Setup;

use Affilicious\Common\Helper\Assert_Helper;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.10.3
 */
class Microdata_Setup
{
    /**
     * Render the product microdata.
     *
     * @hook wp_head
     * @since 0.10.3
     */
    public function init()
    {
	    $this->render_json_ld_microdata();
    }

	/**
	 * Render the product microdata in JSON-LD.
	 *
	 * @since 0.10.3
	 */
    protected function render_json_ld_microdata()
    {
	    /**
         * Filter the product microdata for JSON-LD.
         *
	     * @since 0.10.3
         * @param array $json_lds Multiple JSON-LD microdata markups with a unique key.
	     */
	    $json_lds = apply_filters('aff_product_json_ld_microdata', []);

	    Assert_Helper::is_array($json_lds, __METHOD__, 'Expected all JSON-LD microdata to be an array. Got: %s', '0.10.3');

	    // Encode each JSON-LD block and render it into the header.
        foreach($json_lds as $key => $json_ld) {
	        Assert_Helper::is_array($json_ld, __METHOD__, 'Expected the JSON-LD microdata to be an array. Got: %s', '0.10.3');
            Assert_Helper::is_string_not_empty($key, __METHOD__, 'Expected a non empty string for the unique key of the JSON-LD microdata. Got: %s', '0.10.3');

	        $json_ld = json_encode($json_ld);
	        if (empty($json_ld)) {
		        return;
	        }

            printf('<script type="application/ld+json" data-aff-microdata="%s">%s</script>', $key, $json_ld);
        }
    }
}
