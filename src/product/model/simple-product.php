<?php
namespace Affilicious\Product\Model;

use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Simple_Product extends Product implements Excerpt_Aware_Interface, Content_Aware_Interface, Tag_Aware_Interface, Shop_Aware_Interface, Detail_Aware_Interface, Relation_Aware_Interface, Review_Aware_Interface
{
    use Excerpt_Aware_Trait, Content_Aware_Trait, Tag_Aware_Trait, Shop_Aware_Trait, Detail_Aware_Trait, Relation_Aware_Trait, Review_Aware_Trait {
        Tag_Aware_Trait::__construct as private init_tags;
        Shop_Aware_Trait::__construct as private init_shops;
        Detail_Aware_Trait::__construct as private init_details;
        Relation_Aware_Trait::__construct as private init_relations;
    }

    /**
     * @since 0.8
     * @param Name $name
     * @param Slug $slug
     */
    public function __construct(Name $name, Slug $slug)
    {
        parent::__construct($name, $slug, Type::simple());
        $this->init_tags();
        $this->init_shops();
        $this->init_details();
        $this->init_relations();
    }

    /**
     * Check if this simple product is equal to the other one.
     *
     * @since 0.8
     * @param mixed $other
     * @return bool
     */
    public function is_equal_to($other)
    {
        return
            $other instanceof self &&
            parent::is_equal_to($other) &&
            ($this->has_excerpt() && $this->get_excerpt()->is_equal_to($other->get_excerpt()) || !$other->has_excerpt()) &&
            ($this->has_content() && $this->get_content()->is_equal_to($other->get_content()) || !$other->has_content()) &&
            $this->get_tags() == $other->get_tags() &&
            $this->get_shops() == $other->get_shops() &&
            $this->get_details() == $other->get_details() &&
            $this->get_related_products() == $other->get_related_products() &&
            $this->get_related_accessories() == $other->get_related_accessories() &&
            ($this->has_review() && $this->get_review()->is_equal_to($other->get_review()) || !$other->has_review());
    }
}
