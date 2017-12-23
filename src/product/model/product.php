<?php
namespace Affilicious\Product\Model;

use Affilicious\Common\Model\Custom_Value_Aware_Interface;
use Affilicious\Common\Model\Custom_Value_Aware_Trait;
use Affilicious\Common\Model\Image;
use Affilicious\Common\Model\Image_Id;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Name_Aware_Trait;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Model\Slug_Aware_Trait;
use Affilicious\Common\Model\Status;
use Affilicious\Common\Model\Status_Aware_Trait;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Product implements Custom_Value_Aware_Interface
{
    use Name_Aware_Trait, Slug_Aware_Trait, Status_Aware_Trait, Custom_Value_Aware_Trait;

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
    protected $id;

    /**
     * The type of the product like simple, complex or variants.
     *
     * @var Type
     */
    protected $type;

    /**
     * The thumbnail of the product.
     *
     * @var null|Image
     */
    protected $thumbnail;

    /**
     * The images of the product gallery.
     *
     * @var Image[]
     */
    protected $image_gallery;

    /**
     * The date and time of the last update.
     *
     * @var \DateTimeImmutable
     */
    protected $updated_at;

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
        $this->set_status(Status::draft());
        $this->image_gallery = array();
        $this->updated_at = (new \DateTimeImmutable())->setTimestamp(current_time('timestamp'));
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
     * Check if the product has a thumbnail.
     *
     * @since 0.9
     * @return bool
     */
    public function has_thumbnail()
    {
        return $this->thumbnail !== null;
    }

    /**
     * Get the product thumbnail.
     *
     * @since 0.9
     * @return null|Image
     */
    public function get_thumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Set the product thumbnail.
     *
     * @since 0.9
     * @param null|Image $thumbnail
     */
    public function set_thumbnail(Image $thumbnail = null)
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * Check if the product has a thumbnail ID.
     *
     * @deprecated 1.1 Use 'has_thumbnail' instead.
     * @since 0.8
     * @return bool
     */
    public function has_thumbnail_id()
    {
        return $this->thumbnail !== null;
    }

    /**
     * Get the product thumbnail ID.
     *
     * @deprecated 1.1 Use 'get_thumbnail' instead.
     * @since 0.8
     * @return null|Image_Id
     */
    public function get_thumbnail_id()
    {
        return $this->thumbnail;
    }

    /**
     * Set the product thumbnail ID.
     *
     * @deprecated 1.1 Use 'set_thumbnail' instead.
     * @since 0.8
     * @param null|Image_Id $thumbnail_id
     */
    public function set_thumbnail_id(Image_Id $thumbnail_id = null)
    {
        if($thumbnail_id instanceof Image_Id) {
            $thumbnail_id = new Image($thumbnail_id->get_value());
        }

        $this->thumbnail = $thumbnail_id;
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
     * Get the images of the product gallery.
     *
     * @since 0.8
     * @return Image[]
     */
    public function get_image_gallery()
    {
        $image_gallery = array_values($this->image_gallery);

        return $image_gallery;
    }

    /**
     * Set the images of the product gallery.
     * If you do this, the old IDs going to be replaced.
     *
     * @since 0.8
     * @param Image[] $image_gallery
     */
    public function set_image_gallery(array $image_gallery)
    {
        foreach ($image_gallery as $index => $image) {
            if($image instanceof Image_Id) {
                $image_gallery[$index] = new Image($image->get_value());
            }
        }

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
            ($this->has_thumbnail() && $this->get_thumbnail()->is_equal_to($other->get_thumbnail()) || !$other->has_thumbnail()) &&
            $this->get_image_gallery() === $this->get_image_gallery() &&
            $this->get_updated_at() === $other->get_updated_at();
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
