<?php
namespace Affilicious\Common\Customizer;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @see https://css-tricks.com/getting-started-wordpress-customizer/
 */
abstract class Abstract_Customizer implements Customizer_Interface
{
    /**
     * @inheritdoc
     * @since 0.9.10
     */
    public function register(\WP_Customize_Manager $wp_customize)
    {
        $panels = $this->build();
        $this->apply($panels, $wp_customize);
    }

    /**
     * @inheritdoc
     * @since 0.9.10
     */
    public function render()
    {
        $panels = $this->build();
        $styles = $this->get_inline_styles($panels, false);

        // Attach our customizer styles to our stylesheet.
        wp_add_inline_style($this->get_stylesheet_handle(), $styles);
    }

    /**
     * Build the panels, sections, and settings of the customizer.
     *
     * @since 0.9.10
     * @return array
     */
    protected abstract function build();

    /**
     * Apply the panels, sections, and settings of the customizer.
     *
     * @since 0.9.10
     * @param array $panels
     * @param \WP_Customize_Manager $wp_customize
     */
    protected function apply(array $panels, \WP_Customize_Manager $wp_customize)
    {
        // For each panel...
        foreach ($panels as $panel_id => $panel) {

            // Add this panel to the UI.
            $wp_customize->add_panel(
                $panel_id,
                array(
                    'title'       => $panel['title'],
                    'description' => !empty($panel['description']) ? $panel['description'] : null,
                    'priority'    => $panel['priority'],
                )
            );

            // For each section in this panel, add it to the UI and add settings to it.
            foreach($panel['sections'] as $section_id => $section) {

                // Add this section to the UI.
                $wp_customize->add_section(
                    $panel_id . '-' . $section_id,
                    array(
                        'title'       => $section['title'],
                        'description' => !empty($section['description']) ? $section['description'] : null,
                        'priority'    => $section['priority'],
                        'panel'       => $panel_id,
                    )
                );

                // For each setting in this section, add it to the UI.
                foreach($section['settings'] as $setting_id => $setting) {

                    // Start building an array of args for adding the setting.
                    $setting_args = array(
                        'default' => !empty($setting['default']) ? $setting['default'] : '',
	                    'transport' => !empty($setting['transport']) ? $setting['transport'] : 'refresh',
                    );

                    // Sanitize callback
                    if($setting['type'] == 'color' && empty($setting['sanitize_callback'])) {
                    	$setting_args['sanitize_callback'] = 'sanitize_hex_color';
                    }

                    // Sanitize JS callback
	                if($setting['type'] == 'color' && empty($setting['sanitize_js_callback'])) {
		                $setting_args['sanitize_js_callback'] = 'sanitize_hex_color';
	                }

                    // Register the setting.
                    $wp_customize->add_setting(
                        $panel_id . '-' . $section_id . '-' . $setting_id,
                        $setting_args
                    );

                    // Start building an array of args for adding the control.
                    $control_args = array(
                        'label'       => $setting['label'],
                        'section'     => $panel_id . '-' . $section_id,
                        'type'        => $setting['type'],
                        'description' => !empty($setting['description']) ? $setting['description'] : null,
                    );

                    // Settings of the type 'color' get a special type of control.
                    if($setting['type'] == 'color') {
                        $wp_customize->add_control(
                            // This ships with WordPress. It's a color picker.
                            new \WP_Customize_Color_Control(
                                $wp_customize,
                                $panel_id . '-' . $section_id . '-' . $setting_id,
                                $control_args
                            )
                        );

                    // Else, WordPress will use a default control.
                    } else {
                        $wp_customize->add_control(
                            $panel_id . '-' . $section_id . '-' . $setting_id,
                            $control_args
                        );
                    }
                }
            }
        }
    }

    /**
     * Loop through our theme mods and build a string of CSS rules.
     *
     * @since 0.9.10
     * @param array $panels
     * @param boolean $wrap
     * @return string
     */
    protected function get_inline_styles(array $panels, $wrap = true) {

        // This will hold all of our customizer styles.
        $style = '';

        foreach ($panels as $panel_id => $panel) {
            foreach ($panel['sections'] as $section_id => $section) {
                foreach ($section['settings'] as $setting_id => $setting) {
                	if(empty($setting['type']) || $setting['type'] == 'text') {
                		continue;
	                }

                    // Grab the css for this setting.
                    $css_rules = !empty($setting['css']) ? $setting['css'] : [];

                    $value = get_theme_mod(sprintf('%s-%s-%s', $panel_id, $section_id, $setting_id));
                    if(empty($value)) {
                    	if(!isset($setting['default'])) {
                    		continue;
	                    }

                        $value = $setting['default'];
                    }

                    // For each css rule...
                    foreach ($css_rules as $css_rule) {

                        // The css selector.
                        $selector = $css_rule['selector'];

                        // The css property.
                        $property = $css_rule['property'];

                        $valency = !empty($css_rule['valency']) ? $css_rule['valency'] : '';

                        // Build this into a CSS rule.
                        $rule_string = "$selector { $property : $value $valency ; }";

                        // Does this css rule have media queries?
                        if (isset($css_rule['queries'])) {

                            $query_count = count($css_rule['queries']);

                            $i = 1;
                            $query = '';
                            foreach ($css_rule['queries'] as $query_key => $query_value) {

                                // Add the media query key and value.
                                $query .= "( $query_key : $query_value )";

                                // If this isn't the last query, add the "and" operator.
                                if ($i < $query_count) {
                                    $query .= ' and ';
                                }

                                $i++;
                            }

                            // Wrap the rule string in the media query.
                            $rule_string = " @media $query { $rule_string } ";

                        }

                        // Add the rule, which might be wrapped in a media query, to the output.
                        $style .= $rule_string;
                    }
                }
            }
        }

        if($wrap) {
            $style = sprintf('<style type="text/css">%s</style>', $style);
        }

        return $style;
    }
}
