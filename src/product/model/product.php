<?php
namespace Affilicious\Product\Model;

use Affilicious\Common\Model\Image_Id;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Name_Aware_Trait;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Slug_Aware_Trait;
use Webmozart\Assert\Assert;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Product
{
    use Name_Aware_Trait, Slug_Aware_Trait;

    /**
     * There is a limit of 20 characters for post types in Wordpress.
     */
    const POST_TYPE = 'aff_product';

    /**
     * The default slug is in English but can be translated into any language in the options.
     */
    const SLUG = 'products';

    /**
     * The unique ID of the product.
     * Note that you just get the ID in Wordpress, if you store a post.
     *
     * @var null|Product_Id
     */
    private $id;

    /**
     * The type of the product like simple, complex or variants.
     *
     * @var Type
     */
    private $type;

    /**
     * The thumbnail ID of the product.
     *
     * @var null|Image_Id
     */
    private $thumbnail_id;

    /**
     * The image IDs of the product gallery.
     *
     * @var Image_Id[]
     */
    private $image_gallery;

    /**
     * The date and time of the last update.
     *
     * @var \DateTimeImmutable
     */
    private $updated_at;

    /**
     * @since 0.8
     * @param Name $name
     * @param Slug $slug
     * @param Type $type
     */
    public function __construct(Name $name, Slug $slug, Type $type)
    {
        $this->set_name($name);
        $this->set_slug($slug);
        $this->type = $type;
        $this->image_gallery = array();
        $this->updated_at = new \DateTimeImmutable('now');
    }

    /**
     * Check if the product has an optional ID.
     *
     * @since 0.8
     * @return bool
     */
    public function has_id()
    {
        return $this->id !== null;
    }

    /**
     * Get the optional product ID
     *
     * @since 0.8
     * @return null|Product_Id
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Set the optional product ID.
     * Note that you just get the ID in Wordpress, if you store a post.
     *
     * @since 0.8
     * @param null|Product_Id $id
     */
    public function set_id(Product_Id $id = null)
    {
        $this->id = $id;
    }

    /**
     * Get the type like simple, complex or variants.
     *
     * @since 0.8
     * @return Type
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * Check if the product has a thumbnail ID.
     *
     * @since 0.8
     * @return bool
     */
    public function has_thumbnail_id()
    {
        return $this->thumbnail_id !== null;
    }

    /**
     * Get the product thumbnail ID.
     *
     * @since 0.8
     * @return null|Image_Id
     */
    public function get_thumbnail_id()
    {
        return $this->thumbnail_id;
    }

    /**
     * Set the product thumbnail ID.
     *
     * @since 0.8
     * @param null|Image_Id $thumbnail_id
     */
    public function set_thumbnail_id(Image_Id $thumbnail_id = null)
    {
        $this->thumbnail_id = $thumbnail_id;
    }

    /**
     * Check if the product gallery contains any image IDs.
     *
     * @since 0.8
     * @return bool
     */
    public function has_image_gallery()
    {
        return !empty($this->image_gallery);
    }

    /**
     * Get the image IDs of the product gallery.
     *
     * @since 0.8
     * @return Image_Id[]
     */
    public function get_image_gallery()
    {
        $image_gallery = array_values($this->image_gallery);

        return $image_gallery;
    }

    /**
     * Set the image IDs of the product gallery.
     * If you do this, the old IDs going to be replaced.
     *
     * @since 0.8
     * @param Image_Id[] $image_gallery
     */
    public function set_image_gallery($image_gallery)
    {
        Assert::allIsInstanceOf($image_gallery, Image_Id::class);

        $this->image_gallery = $image_gallery;
    }

    /**
     * Get the date and time of the last product update.
     *
     * @since 0.8
     * @return \DateTimeImmutable
     */
    public function get_updated_at()
    {
        return $this->updated_at;
    }

    /**
     * Set the date and time of the last product update.
     *
     * @since 0.8
     * @param \DateTimeImmutable $updated_at
     */
    public function set_updated_at(\DateTimeImmutable $updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * Check if this product is equal to the other one.
     *
     * @since 0.8
     * @param mixed $other
     * @return bool
     */
    public function is_equal_to($other)
    {
        return
            $other instanceof self &&
            ($this->has_id() && $this->get_id()->is_equal_to($other->get_id()) || !$other->has_id()) &&
            $this->get_name()->is_equal_to($other->get_name()) &&
            $this->get_slug()->is_equal_to($other->get_slug()) &&
            $this->get_type()->is_equal_to($other->get_type()) &&
            ($this->has_thumbnail_id() && $this->get_thumbnail_id()->is_equal_to($other->get_thumbnail_id()) || !$other->has_thumbnail_id()) &&
            $this->get_image_gallery() == $this->get_image_gallery() &&
            $this->get_updated_at() == $other->get_updated_at();
    }

    /**
     * Get the raw Wordpress post of the product.
     *
     * @since 0.8.2
     * @param string $output
     * @param string $filter
     * @return array|null|\WP_Post
     */
    public function get_post($output = OBJECT, $filter = 'raw')
    {
        if(!$this->has_id()) {
            return null;
        }

        $term = get_post($this->id->get_value(), $output, $filter);

        return $term;
    }
}
