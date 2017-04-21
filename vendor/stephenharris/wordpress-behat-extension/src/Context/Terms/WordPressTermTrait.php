<?php
namespace StephenHarris\WordPressBehatExtension\Context\Terms;

/**
 * A 'helper' class primarily used by WordPressTermsContext which holds the step definitions.
 *
 * This class has been seperated out from the step definitions so that it can be re-used for other contexts
 *
 * @package StephenHarris\WordPressBehatExtension\Context
 */
trait WordPressTermTrait
{

    public function insert($termData, $taxonomy)
    {
        if (! isset($termData['name'])) {
            throw new \InvalidArgumentException('Term requires a name.');
        }
        $term_ids = wp_insert_term($termData['name'], $taxonomy, $termData);
        if (is_wp_error($term_ids)) {
            throw new \InvalidArgumentException(
                sprintf("Invalid taxonomy term information schema: %s", $return->get_error_message())
            );
        }
        return $term_ids;
    }

    public function getTerm($name_or_slug, $taxonomy)
    {
        try {
            $term = $this->getTermByName($name_or_slug, $taxonomy);
        } catch (\Exception $e) {
            try {
                $term = $this->getTermBySlug($name_or_slug, $taxonomy);
            } catch (\Exception $e) {
                throw new \Exception(
                    sprintf('No term with name or slug "%s" in %s taxonomy found', $name_or_slug, $taxonomy)
                );
            }
        }
        return $term;
    }

    public function getTermByName($name, $taxonomy)
    {
        $term = get_term_by('name', $name, $taxonomy);
        if (! $term) {
            throw new \Exception(
                sprintf('No term with name "%s" in %s taxonomy found', $name, $taxonomy)
            );
        }
        return $term;
    }

    public function getTermBySlug($name, $taxonomy)
    {
        $term = get_term_by('name', $name, $taxonomy);
        if (! $term) {
            throw new \Exception(
                sprintf('No term with slug "%s" in %s taxonomy found', $name, $taxonomy)
            );
        }
        return $term;
    }
}
