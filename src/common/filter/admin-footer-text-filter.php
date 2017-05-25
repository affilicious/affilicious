<?php
namespace Affilicious\Common\Filter;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Admin_Footer_Text_Filter
{
    /**
     * Append the Affilicious thank you text.
     *
     * @hook admin_footer_text
     * @since 0.8
     * @param string $text
     * @return string
     */
    public function filter($text)
    {
        $thank_you = sprintf(
            __('Thank you for creating with <a href="%s" target="_blank">Affilicious</a>.', 'affilicious'),
            'https://affilicioustheme.com'
        );

        $text .= sprintf(' | <span id="aff-footer-thank-you">%s</span>', $thank_you);

        return $text;
    }
}
