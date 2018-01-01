<?php
namespace Affilicious\Common\Model;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Term
{
    use Slug_Aware_Trait {
        Slug_Aware_Trait::set_slug as private;
    }

    /**
     * @var Taxonomy
     */
    protected $taxonomy;

    /**
     * @since 0.9.16
     * @param Slug $slug
     * @param Taxonomy $taxonomy
     */
    public function __construct(Slug $slug, Taxonomy $taxonomy)
    {
        $this->set_slug($slug);
        $this->taxonomy = $taxonomy;
    }

    /**
     * @since 0.9.16
     * @return Taxonomy
     */
    public function get_taxonomy()
    {
        return $this->taxonomy;
    }

    /**
     * Get all term data from database by term ID.
     *
     * @since 0.9.16
     * @param string $output Optional. The required return type. One of OBJECT, ARRAY_A, or ARRAY_N, which correspond to a WP_Term object, an associative array, or a numeric array, respectively. Default OBJECT.
     * @param string $filter Optional. Default is raw or no WordPress defined filter will applied.
     * @return array|\WP_Term|\WP_Error|null Object of the type specified by `$output` on success. When `$output` is 'OBJECT', a WP_Term instance is returned. If taxonomy does not exist, a WP_Error is returned. Returns null for miscellaneous failure.
     */
    public function get_term($output = OBJECT, $filter = 'raw')
    {
        $term = get_term(
            $this->get_slug()->get_value(),
            $this->taxonomy->get_slug()->get_value(),
            $output,
            $filter
        );

        return $term;
    }
}
