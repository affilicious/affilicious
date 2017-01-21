<?php
namespace Affilicious\Common\Model;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

trait Help_Text_Trait
{
    /**
     * Holds the optional help text for a better description.
     *
     * @var null|Help_Text
     */
    protected $help_text;

    /**
     * Check if the optional help text exists.
     *
     * @since 0.8
     * @return bool
     */
    public function has_help_text()
    {
        return $this->help_text !== null;
    }

    /**
     * Get the optional help text
     *
     * @since 0.8
     * @return null|Help_Text
     */
    public function get_help_text()
    {
        return $this->help_text;
    }

    /**
     * Set the optional help text
     *
     * @since 0.8
     * @param null|Help_Text $help_text
     */
    public function set_help_text(Help_Text $help_text = null)
    {
        $this->help_text = $help_text;
    }
}
