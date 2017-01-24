<?php
namespace Affilicious\Product\Model;

use Webmozart\Assert\Assert;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

trait Tag_Aware_Trait
{
    /**
     * The product tags like "Test winner" or "Best price"
     *
     * @var Tag[]
     */
    private $tags;

    /**
     * @since 0.8
     */
    public function __construct()
    {
        $this->tags = array();
    }

    /**
     * Check if the product has any tags.
     *
     * @since 0.8
     * @return bool
     */
    public function has_tags()
    {
        return !empty($this->tags);
    }

    /**
     * Get the product tags.
     *
     * @since 0.8
     * @return Tag[]
     */
    public function get_tags()
    {
        $tags = array_values($this->tags);

        return $tags;
    }

    /**
     * Set the product tags.
     * If you do this, the old tags going to be replaced.
     *
     * @since 0.8
     * @param Tag[] $tags
     */
    public function set_tags($tags)
    {
        Assert::allIsInstanceOf($tags, Tag::class);

        $this->tags = $tags;
    }
}
