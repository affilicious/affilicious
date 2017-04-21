<?php
namespace StephenHarris\WordPressBehatExtension\Context\PostTypes;

/**
 * A 'helper' class primarily used by WordPressPostContext which holds the step definitions.
 *
 * This class has been seperated out from the step definitions so that it can be re-used for other contexts (i.e. third
 * party post type contexts).
 *
 * @package StephenHarris\WordPressBehatExtension\Context
 */
trait WordPressPostTrait
{

    public function insert($postData)
    {
        $post_id = wp_insert_post($postData, true);
        if (!is_int($post_id)) {
            throw new \InvalidArgumentException("Invalid post information schema: " . $post_id->get_error_message());
        }
        return $post_id;
    }

    public function getPostByName($title, $postType = null)
    {
        if (is_null($postType)) {
            $postType = get_post_types('', 'names');
        }
        $post = get_page_by_title($title, OBJECT, $postType);
        if (! $post) {
            if (is_array($postType)) {
                $postType = implode('/', $postType);
            }
            throw new \Exception(
                sprintf('Post "%s" of post type %s not found', $title, $postType)
            );
        }
        return $post;
    }

    public function assignPostTypeTerms($post, $taxonomy, $terms)
    {

        $term_ids = wp_set_object_terms($post->ID, $terms, $taxonomy, false);

        if (! $term_ids) {
            throw new \Exception(
                sprintf('Could not set the %s terms of post "%s"', $taxonomy, $post->post_title)
            );
        } elseif (is_wp_error($term_ids)) {
            throw new \Exception(
                sprintf(
                    'Could not set the %s terms of post "%s": %s',
                    $taxonomy,
                    $post->post_title,
                    $terms->get_error_message()
                )
            );
        }
    }

    public function assertPostTypeTerms($post, $taxonomy, $term_slugs)
    {
        clean_post_cache($post->ID);
        $actual_terms = get_the_terms($post->ID, $taxonomy);

        if (! $actual_terms) {
            throw new \InvalidArgumentException(
                sprintf('Could not get the %s terms of post "%s"', $taxonomy, $post->post_title)
            );
        } elseif (is_wp_error($term_slugs)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Could not get the %s terms of post "%s": %s',
                    $taxonomy,
                    $post->post_title,
                    $term_slugs->get_error_message()
                )
            );
        }

        $actual_slugs   = wp_list_pluck($actual_terms, 'slug');
        $expected_slugs = array_map('trim', explode(',', $term_slugs));

        $does_not_have   = array_diff($expected_slugs, $actual_slugs);
        $should_not_have = array_diff($actual_slugs, $expected_slugs);

        if ($does_not_have || $should_not_have) {
            throw new \Exception(
                sprintf(
                    'Failed asserting "%s" has the %s terms: "%s"' . "\n" . "Actual terms: %s",
                    $post->post_title,
                    $taxonomy,
                    implode(',', $expected_slugs),
                    implode(',', $actual_slugs)
                )
            );
        }
    }

    public function assertPostTypeStatus($post, $status)
    {
        clean_post_cache($post->ID);
        $actual_status = get_post_status($post->ID);

        \PHPUnit_Framework_Assert::assertTrue(
            $status,
            $actual_status,
            "The post status does not match the expected status"
        );
    }
}
