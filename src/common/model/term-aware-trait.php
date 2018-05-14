<?php
namespace Affilicious\Common\Model;

use Affilicious\Common\Helper\Assert_Helper;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.16
 */
trait Term_Aware_Trait
{
    /**
     * @since 0.9.16
     * @var Term[] All object terms.
     */
    protected $terms = [];

    /**
     * Check if the object contains a term with the slug.
     *
     * @since 0.9.16
     * @param Slug $slug The term slug.
     * @return boolean Whether the term exists or not.
     */
    public function has_term(Slug $slug)
    {
        return isset($this->terms[$slug->get_value()]);
    }

    /**
     * Add the term to the object.
     *
     * @since 0.9.16
     * @param Term $term The term which contains the slug.
     */
    public function add_term(Term $term)
    {
        $this->terms[$term->get_slug()->get_value()] = $term;
    }

    /**
     * Remove the term from the object by the slug.
     *
     * @since 0.9.16
     * @param Slug $slug The term slug.
     */
    public function remove_term(Slug $slug)
    {
        unset($this->terms[$slug->get_value()]);
    }

    /**
     * Get all terms from the object.
     *
     * @since 0.9.16
     * @return Term[] All terms from the object
     */
    public function get_terms()
    {
        $terms = array_values($this->terms);

        return $terms;
    }

    /**
     * Set all terms for the object.
     *
     * @since 0.9.16
     * @param Term[] $terms All terms for the object.
     */
    public function set_terms(array $terms)
    {
        Assert_Helper::all_is_instance_of($terms, Term::class, __METHOD__, 'Expected an array of terms. But one of the values is %s', '1.0');

        $this->terms = $terms;
    }
}
