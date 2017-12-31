<?php
namespace Affilicious\Common\Model;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Taxonomy
{
    use Slug_Aware_Trait {
        Slug_Aware_Trait::set_slug as private;
    }

    /**
     * @since 0.9.16
     * @param Slug $slug
     */
    public function __construct(Slug $slug)
    {
        $this->set_slug($slug);
    }

    /**
     * Retrieves the taxonomy object of $taxonomy.
     *
     * @since 0.9.16
     * @return \WP_Taxonomy|false The taxonomy object or false if the taxonomy doesn't exist.
     */
    public function get_taxonomy()
    {
        $taxonomy = get_taxonomy($this->get_slug()->get_value());

        return $taxonomy;
    }
}
