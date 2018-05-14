<?php
namespace Affilicious\Common\Filter;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8.14
 */
class Link_Target_Filter
{
    /**
     * This filter removes the "noopener noreferrer" attribute from links, which
     * has been introduced in Wordpress 4.7.4. This attribute is unfavorable for
     * affiliate marketers as it lowers the provision due to missing tracking.
     *
     *
     * @filter tiny_mce_before_init
     * @since 0.8.14
     * @param array $mceInit
     * @return array
     */
    public function filter($mceInit)
    {
        $mceInit['allow_unsafe_link_target'] = true;

        return $mceInit;
    }
}
