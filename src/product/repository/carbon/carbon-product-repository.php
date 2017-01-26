<?php
namespace Affilicious\Product\Repository\Carbon;

use Affilicious\Attribute\Model\Value as Attribute_Value;
use Affilicious\Attribute\Repository\Attribute_Template_Repository_Interface;
use Affilicious\Common\Exception\Invalid_Post_Type_Exception;
use Affilicious\Common\Exception\Invalid_Type_Exception;
use Affilicious\Common\Generator\Key_Generator_Interface;
use Affilicious\Common\Generator\Slug_Generator_Interface;
use Affilicious\Common\Model\Image_Id;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Common\Repository\Carbon\Abstract_Carbon_Repository;
use Affilicious\Detail\Model\Value as Detail_Value;
use Affilicious\Detail\Repository\Detail_Template_Repository_Interface;
use Affilicious\Product\Exception\Failed_To_Delete_Product_Exception;
use Affilicious\Product\Exception\Missing_Parent_Product_Exception;
use Affilicious\Product\Exception\Product_Not_Found_Exception;
use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Model\Content;
use Affilicious\Product\Model\Content_Aware_Interface;
use Affilicious\Product\Model\Detail_Aware_Interface;
use Affilicious\Product\Model\Excerpt;
use Affilicious\Product\Model\Excerpt_Aware_Interface;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Product_Id;
use Affilicious\Product\Model\Product_Variant;
use Affilicious\Product\Model\Rating;
use Affilicious\Product\Model\Relation_Aware_Interface;
use Affilicious\Product\Model\Review;
use Affilicious\Product\Model\Review_Aware_Interface;
use Affilicious\Product\Model\Shop_Aware_Interface;
use Affilicious\Product\Model\Simple_Product;
use Affilicious\Product\Model\Tag;
use Affilicious\Product\Model\Tag_Aware_Interface;
use Affilicious\Product\Model\Type;
use Affilicious\Product\Model\Votes;
use Affilicious\Product\Repository\Product_Repository_Interface;
use Affilicious\Shop\Model\Affiliate_Id;
use Affilicious\Shop\Model\Affiliate_Link;
use Affilicious\Shop\Model\Availability;
use Affilicious\Shop\Model\Currency;
use Affilicious\Shop\Model\Money;
use Affilicious\Shop\Model\Pricing;
use Affilicious\Shop\Model\Shop;
use Affilicious\Shop\Model\Shop_Template_Id;
use Affilicious\Shop\Model\Tracking;
use Affilicious\Shop\Repository\Shop_Template_Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Carbon_Product_Repository extends Abstract_Carbon_Repository implements Product_Repository_Interface
{
    const TYPE = '_affilicious_product_type';
    const TAGS = '_affilicious_product_tags';
    const IMAGE_GALLERY = '_affilicious_product_image_gallery';

    const SHOPS = '_affilicious_product_shops';
    const SHOP_TEMPLATE_ID = 'template_id';
    const SHOP_DISCOUNTED_PRICE = 'discounted_price';
    const SHOP_STOCK_PRICE = 'stock_price';
    const SHOP_CURRENCY = 'currency';
    const SHOP_AVAILABILITY = 'availability';
    const SHOP_AFFILIATE_ID = 'affiliate_id';
    const SHOP_AFFILIATE_LINK = 'affiliate_link';
    const SHOP_UPDATED_AT = 'updated_at';

    const ENABLED_DETAILS = '_affilicious_product_enabled_details';
    const DETAIL_VALUE = '_affilicious_product_detail_%s_value';

    const ENABLED_ATTRIBUTES = '_affilicious_product_enabled_attributes';
    const VARIANTS = '_affilicious_product_variants';
    const VARIANT_ENABLED_ATTRIBUTES = 'enabled_attributes';
    const VARIANT_ID = 'variant_id';
    const VARIANT_NAME = 'name';
    const VARIANT_DEFAULT = 'default';
    const VARIANT_TAGS = 'tags';
    const VARIANT_THUMBNAIL_ID = 'thumbnail_id';
    const VARIANT_ATTRIBUTE_VALUE = 'attribute_%s_value';
    const VARIANT_IMAGE_GALLERY = 'image_gallery';
    const VARIANT_SHOPS = 'shops';

    const ATTRIBUTE_VALUE = '_affilicious_product_attribute_%s_value';

    const REVIEW_RATING = '_affilicious_product_review_rating';
    const REVIEW_VOTES = '_affilicious_product_review_votes';

    const RELATED_PRODUCTS = '_affilicious_product_related_products';
    const RELATED_ACCESSORIES = '_affilicious_product_related_accessories';

    /**
     * @var Slug_Generator_Interface
     */
    private $slug_generator;

    /**
     * @var Key_Generator_Interface
     */
    private $key_generator;

    /**
     * @var Shop_Template_Repository_Interface
     */
    private $shop_template_repository;

    /**
     * @var Attribute_Template_Repository_Interface
     */
    private $attribute_template_repository;

    /**
     * @var Detail_Template_Repository_Interface
     */
    private $detail_template_repository;

    /**
     * @since 0.8
     * @param Slug_Generator_Interface $slug_generator
     * @param Key_Generator_Interface $key_generator
     * @param Shop_Template_Repository_Interface $shop_template_repository
     * @param Attribute_Template_Repository_Interface $attribute_template_repository
     * @param Detail_Template_Repository_Interface $detail_template_repository
     */
    public function __construct(
        Slug_Generator_Interface $slug_generator,
        Key_Generator_Interface $key_generator,
        Shop_Template_Repository_Interface $shop_template_repository,
        Attribute_Template_Repository_Interface $attribute_template_repository,
        Detail_Template_Repository_Interface $detail_template_repository
    )
    {
        $this->slug_generator = $slug_generator;
        $this->key_generator = $key_generator;
        $this->shop_template_repository = $shop_template_repository;
        $this->attribute_template_repository = $attribute_template_repository;
        $this->detail_template_repository = $detail_template_repository;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function store(Product $product)
    {
        // Product variants must have a parent product
        if($product instanceof Product_Variant && !$product->get_parent()->has_id()) {
            throw new Missing_Parent_Product_Exception($product->get_id());
        }

        // Store the product into the database.
        $default_args = $this->get_default_args($product);
        $args = $this->parse_args($product, $default_args);
        $id = !empty($args['id']) ? wp_update_post($args) : wp_insert_post($args);

        // The ID and the name might have changed in the database. Update both values.
        if(empty($default_args)) {
            $post = get_post($id, OBJECT);
            $product->set_id(new Product_Id($post->ID));
            $product->set_slug(new Slug($post->post_name));
        }

        // Store the product meta
        $this->store_type($product);
        $this->store_thumbnail($product);
        $this->store_image_gallery($product);

        if($product instanceof Detail_Aware_Interface) {
            $this->store_details($product);
        }

        if($product instanceof Shop_Aware_Interface) {
            $this->store_shops($product, self::SHOPS);
        }

        if($product instanceof Review_Aware_Interface) {
            $this->store_review($product);
        }

        if($product instanceof Product_Variant) {
            $this->store_attributes($product);
        }

        if($product instanceof Complex_Product) {
            $this->store_variants($product);
        }

        if($product instanceof Tag_Aware_Interface) {
            $this->store_tags($product);
        }

        return $product;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function store_all($products)
    {
        $stored_products = array();
        foreach ($products as $product) {
            $stored_product = $this->store($product);
            $stored_products[] = $stored_product;
        }

        return $stored_products;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function delete(Product_Id $product_id)
    {
        $post = get_post($product_id->get_value());
        if (empty($post)) {
            throw new Product_Not_Found_Exception($product_id);
        }

        if($post->post_type != Product::POST_TYPE) {
            throw new Invalid_Post_Type_Exception($post->post_type, Product::POST_TYPE);
        }

        $post = wp_delete_post($product_id->get_value(), false);
        if(empty($post)) {
            throw new Failed_To_Delete_Product_Exception($product_id);
        }

        $product = $this->build_product_from_post($post);
        $product->set_id(null);

        return $product;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function delete_all($products)
    {
        $deleted_products = array();
        foreach ($products as $product) {
            if($product instanceof Product && $product->has_id()) {
                $deleted_product = $this->delete($product->get_id());
                $deleted_products[] = $deleted_product;
            }
        }

        return $deleted_products;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function find_by_id(Product_Id $product_id)
    {
        $post = get_post($product_id->get_value());
        if (empty($post) || $post->post_status !== 'publish') {
            return null;
        }

        $product = self::build_product_from_post($post);

        return $product;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function find_all()
    {
        $query = new \WP_Query(array(
            'post_type' => Product::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        $products = array();
        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $product = self::build_product_from_post($query->post);
                $products[] = $product;
            }

            wp_reset_postdata();
        }

        return $products;
    }

    /**
     * Convert the Wordpress post into a product.
     *
     * @since 0.6
     * @param \WP_Post $post
     * @return Product
     */
    private function build_product_from_post(\WP_Post $post)
    {
        if($post->post_type !== Product::POST_TYPE) {
            throw new Invalid_Post_Type_Exception($post->post_type, Product::POST_TYPE);
        }

        // Get the type like simple, complex or variant.
        $type = $this->get_type($post);

        $product = null;
        switch($type->get_value()) {
            case Type::SIMPLE:
                $product = $this->build_simple_product_from_post($post);
                break;
            case Type::COMPLEX:
                $product = $this->build_complex_product_from_post($post);
                break;
            case Type::VARIANT:
                $product = $this->build_product_variant_from_post($post);
                break;
            default:
                break;
        }

        return $product;
    }

    /**
     * Convert the Wordpress post into a simple product.
     *
     * @since 0.7
     * @param \WP_Post $post
     * @return Simple_Product
     */
    private function build_simple_product_from_post(\WP_Post $post)
    {
        if($post->post_type !== Product::POST_TYPE) {
            throw new Invalid_Post_Type_Exception($post->post_type, Product::POST_TYPE);
        }

        $title = new Name($post->post_title);
        $slug = new Slug($post->post_name);
        $key = $this->key_generator->generate_from_slug($slug);

        $simple_product = new Simple_Product($title, $slug, $key);
        $simple_product = $this->add_id($simple_product, $post);
        $simple_product = $this->add_content($simple_product, $post);
        $simple_product = $this->add_excerpt($simple_product, $post);
        $simple_product = $this->add_thumbnail($simple_product, $post);
        $simple_product = $this->add_shops($simple_product);
        $simple_product = $this->add_tags($simple_product);
        $simple_product = $this->add_details($simple_product);
        $simple_product = $this->add_review($simple_product, $post);
        $simple_product = $this->add_related_products($simple_product, $post);
        $simple_product = $this->add_related_accessories($simple_product, $post);
        $simple_product = $this->add_image_gallery($simple_product, $post);
        $simple_product = $this->add_updated_at($simple_product, $post);

        return $simple_product;
    }

    /**
     * Convert the Wordpress post into a complex product.
     *
     * @since 0.7
     * @param \WP_Post $post
     * @return Complex_Product
     */
    private function build_complex_product_from_post(\WP_Post $post)
    {
        if($post->post_type !== Product::POST_TYPE) {
            throw new Invalid_Post_Type_Exception($post->post_type, Product::POST_TYPE);
        }

        $title = new Name($post->post_title);
        $slug = new Slug($post->post_name);
        $key = $this->key_generator->generate_from_slug($slug);

        $complex_product = new Complex_Product($title, $slug, $key);
        $complex_product = $this->add_id($complex_product, $post);
        $complex_product = $this->add_content($complex_product, $post);
        $complex_product = $this->add_excerpt($complex_product, $post);
        $complex_product = $this->add_thumbnail($complex_product, $post);
        $complex_product = $this->add_variants($complex_product);
        $complex_product = $this->add_review($complex_product, $post);
        $complex_product = $this->add_details($complex_product);
        $complex_product = $this->add_related_products($complex_product, $post);
        $complex_product = $this->add_related_accessories($complex_product, $post);
        $complex_product = $this->add_image_gallery($complex_product, $post);
        $complex_product = $this->add_updated_at($complex_product, $post);

        return $complex_product;
    }

    /**
     * Convert the Wordpress post into a product variant.
     *
     * @since 0.7
     * @param \WP_Post $post
     * @param Complex_Product $parent
     * @return Product_Variant
     */
    private function build_product_variant_from_post(\WP_Post $post, Complex_Product $parent = null)
    {
        if($post->post_type !== Product::POST_TYPE) {
            throw new Invalid_Post_Type_Exception($post->post_type, Product::POST_TYPE);
        }

        if($parent === null) {
            $parent = $this->get_parent_complex_product(new Product_Id($post->ID));
        }

        if($parent === null) {
            throw new \RuntimeException(sprintf(
                'Failed to find the parent complex product for the product variant #%s (%s).',
                $post->ID,
                $post->post_title
            ));
        }

        $title = new Name($post->post_title);
        $slug = new Slug($post->post_name);
        $key = $this->key_generator->generate_from_slug($slug);

        $product_variant = new Product_Variant($parent, $title, $slug, $key);
        $product_variant = $this->add_id($product_variant, $post);
        $product_variant = $this->add_thumbnail($product_variant, $post);
        $product_variant = $this->add_shops($product_variant);
        $product_variant = $this->add_tags($product_variant);
        $product_variant = $this->add_image_gallery($product_variant, $post);
        $product_variant = $this->add_updated_at($product_variant, $post);

        return $product_variant;
    }

    /**
     * Find the parent complex product of the product variant by the given ID.
     *
     * @since 0.6
     * @param Product_Id $product_variant_id
     * @return null|Complex_Product
     */
    private function get_parent_complex_product(Product_Id $product_variant_id)
    {
        $parent_post_id = wp_get_post_parent_id($product_variant_id->get_value());
        if(empty($parent_post_id)) {
            return null;
        }

        $parent_post = get_post($parent_post_id);
        if(empty($parent_post)) {
            return null;
        }

        $complex_product = $this->build_complex_product_from_post($parent_post);

        return $complex_product;
    }

    /**
     * Add the ID to the product.
     *
     * @since 0.7
     * @param Product $product
     * @param \WP_Post $post
     * @return Product
     */
    private function add_id(Product $product, \WP_Post $post)
    {
        $product->set_id(new Product_Id($post->ID));

        return $product;
    }

    /**
     * Add the thumbnail to the product.
     *
     * @since 0.7
     * @param Product $product
     * @param \WP_Post $post
     * @return Product
     */
    private function add_thumbnail(Product $product, \WP_Post $post)
    {
        $thumbnail_id = get_post_thumbnail_id($post->ID);
        if (!empty($thumbnail_id)) {
            $thumbnail = new Image_Id($thumbnail_id);
            $product->set_thumbnail_id($thumbnail);
        }

        return $product;
    }

    /**
     * Add the excerpt to the product.
     *
     * @since 0.7
     * @param Product $product
     * @param \WP_Post $post
     * @return Product
     */
    private function add_excerpt(Product $product, \WP_Post $post)
    {
        if(!($product instanceof Excerpt_Aware_Interface)) {
            return $product;
        }

        $excerpt = $post->post_excerpt;
        if(!empty($excerpt)) {
            $product->set_excerpt(new Excerpt($excerpt));
        }

        return $product;
    }

    /**
     * Add the content to the product.
     *
     * @since 0.7
     * @param Product $product
     * @param \WP_Post $post
     * @return Product
     */
    private function add_content(Product $product, \WP_Post $post)
    {
        if(!($product instanceof Content_Aware_Interface)) {
            return $product;
        }

        $content = $post->post_content;
        if(!empty($content)) {
            $product->set_content(new Content($content));
        }

        return $product;
    }

    /**
     * Add shops to the product.
     *
     * @since 0.7
     * @param Product $product
     * @param array $raw_shops
     * @return Product
     */
    private function add_shops(Product $product, $raw_shops = array())
    {
        if (!($product instanceof Shop_Aware_Interface)) {
            return $product;
        }

        if(empty($raw_shops)) {
            $raw_shops = carbon_get_post_meta($product->get_id()->get_value(), self::SHOPS, 'complex');
        }

        if (!empty($raw_shops)) {
            foreach ($raw_shops as $raw_shop) {
                $shop = self::get_shop_from_array($raw_shop);

                if ($shop !== null) {
                    $product->add_shop($shop);
                }
            }
        }

        return $product;
    }

    /**
     * Add the variants to the complex product.
     *
     * @since 0.7
     * @param Complex_Product $complex_product
     * @return Complex_Product
     */
    private function add_variants(Complex_Product $complex_product)
    {
        $raw_variants = carbon_get_post_meta($complex_product->get_id()->get_value(), self::VARIANTS, 'complex');

        foreach ($raw_variants as $raw_variant) {
            $id = !empty($raw_variant[self::VARIANT_ID]) ? $raw_variant[self::VARIANT_ID] : null;
            $name = !empty($raw_variant[self::VARIANT_NAME]) ? $raw_variant[self::VARIANT_NAME] : null;
            $thumbnail_id = !empty($raw_variant[self::VARIANT_THUMBNAIL_ID]) ? $raw_variant[self::VARIANT_THUMBNAIL_ID] : null;
            $shops = !empty($raw_variant[self::VARIANT_SHOPS]) ? $raw_variant[self::VARIANT_SHOPS] : null;
            $tags = !empty($raw_variant[self::VARIANT_TAGS]) ? $raw_variant[self::VARIANT_TAGS] : null;
            $default = !empty($raw_variant[self::VARIANT_DEFAULT]) ? $raw_variant[self::VARIANT_DEFAULT] : null;
            $image_gallery = !empty($raw_variant[self::VARIANT_IMAGE_GALLERY]) ? $raw_variant[self::VARIANT_IMAGE_GALLERY] : null;

            if(empty($name)) {
                continue;
            }

            $post = get_post($id);
            $name = new Name($name);
            $slug = $id !== null ? new Slug($post->post_name) : $this->slug_generator->generate_from_name($name);
            $product_variant = new Product_Variant($complex_product, $name, $slug);

            if(!empty($id)) {
                $product_variant->set_id(new Product_Id($id));
            }

            $attribute_templates = $this->attribute_template_repository->find_all();
            foreach ($attribute_templates as $attribute_template) {
                $attribute_template_key = $this->key_generator->generate_from_slug($attribute_template->get_slug());
                $meta_key = sprintf(self::VARIANT_ATTRIBUTE_VALUE, $attribute_template_key->get_value());

                $raw_attribute = !empty($raw_variant[$meta_key]) ? $raw_variant[$meta_key] : null;
                if(empty($raw_attribute)) {
                    continue;
                }

                $attribute = $attribute_template->build(new Attribute_Value($raw_attribute));
                $product_variant->add_attribute($attribute);
            }

            if(!empty($thumbnail_id)) {
                $product_variant->set_thumbnail_id(new Image_Id($thumbnail_id));
            }

            if(!empty($default) && $default === 'yes') {
                $product_variant->set_default(true);
            }

            if(!empty($shops)) {
                $this->add_shops($product_variant, $shops);
            }

            if(!empty($tags)) {
                $this->add_tags($product_variant, $tags);
            }

            if(!empty($image_gallery)) {
                $image_ids = explode(',', $image_gallery);

                $images = array();
                foreach ($image_ids as $image_id) {
                    $images[] = new Image_Id($image_id);
                }

                $product_variant->set_image_gallery($images);
            }

            $complex_product->add_variant($product_variant);
        }

        return $complex_product;
    }

    /**
     * Add the review to the product.
     *
     * @since 0.7
     * @param Product $product
     * @param \WP_Post $post
     * @return Product
     */
    private function add_review(Product $product, \WP_Post $post)
    {
        if(!($product instanceof Review_Aware_Interface)) {
            return $product;
        }

        $rating = carbon_get_post_meta($post->ID, self::REVIEW_RATING);
        if((!empty($rating) || $rating == '0') && $rating !== 'none') {
            $review = new Review(new Rating($rating));

            $votes = carbon_get_post_meta($post->ID, self::REVIEW_VOTES);
            if (!empty($votes)) {
                $review->set_votes(new Votes($votes));
            }

            $product->set_review($review);
        }

        return $product;
    }

    /**
     * Add related products to the product.
     *
     * @since 0.7
     * @param Product $product
     * @param \WP_Post $post
     * @return Product
     */
    private function add_related_products(Product $product, \WP_Post $post)
    {
        if(!($product instanceof Relation_Aware_Interface)) {
            return $product;
        }

        $related_products = carbon_get_post_meta($post->ID, self::RELATED_PRODUCTS);
        if (!empty($related_products)) {
            $related_products = array_map(function ($value) {
                return new Product_Id($value);
            }, $related_products);

            $product->set_related_products($related_products);
        }

        return $product;
    }

    /**
     * Add related accessories to the product.
     *
     * @since 0.7
     * @param Product $product
     * @param \WP_Post $post
     * @return Product
     */
    private function add_related_accessories(Product $product, \WP_Post $post)
    {
        if(!($product instanceof Relation_Aware_Interface)) {
            return $product;
        }

        $related_accessories = carbon_get_post_meta($post->ID, self::RELATED_ACCESSORIES);
        if (!empty($related_accessories)) {
            $related_accessories = array_map(function ($value) {
                return new Product_Id($value);
            }, $related_accessories);

            $product->set_related_accessories($related_accessories);
        }

        return $product;
    }

    /**
     * Add the image gallery to the product.
     *
     * @since 0.7
     * @param Product $product
     * @param \WP_Post $post
     * @return Product
     */
    private function add_image_gallery(Product $product, \WP_Post $post)
    {
        $image_gallery = get_post_meta($post->ID, self::IMAGE_GALLERY);
        if (!empty($image_gallery) && strlen($image_gallery[0]) > 0) {
            $image_ids = explode(',', $image_gallery[0]);

            $images = array();
            foreach ($image_ids as $image_id) {
                $images[] = new Image_Id($image_id);
            }

            $product->set_image_gallery($images);
        }

        return $product;
    }

    /**
     * Add the tags to the product.
     *
     * @since 0.6
     * @param Product $product
     * @param array $raw_tags
     * @return Product
     */
    private function add_tags(Product $product, $raw_tags = array())
    {
        if(!($product instanceof Tag_Aware_Interface)) {
            return $product;
        }

        if(empty($raw_tags)) {
            $raw_tags = carbon_get_post_meta($product->get_id()->get_value(), self::TAGS);
        }

        if(!empty($raw_tags)) {
            $raw_tags = explode(';', $raw_tags);
            $tags = array_map(function($raw_tag) {
                return new Tag($raw_tag);
            }, $raw_tags);

            $product->set_tags($tags);
        }

        return $product;
    }

    /**
     * Add the details to the product.
     *
     * @since 0.8
     * @param Product $product
     * @return Product
     */
    private function add_details(Product $product)
    {
        if(!($product instanceof Detail_Aware_Interface)) {
            return $product;
        }

        $detail_templates = $this->detail_template_repository->find_all();
        foreach ($detail_templates as $detail_template) {
            $detail_template_key = $this->key_generator->generate_from_slug($detail_template->get_slug());
            $meta_key = sprintf(self::DETAIL_VALUE, $detail_template_key->get_value());

            $raw_detail = carbon_get_post_meta($product->get_id()->get_value(), $meta_key);
            if(empty($raw_detail)) {
                continue;
            }

            $detail = $detail_template->build(new Detail_Value($raw_detail));
            $product->add_detail($detail);
        }

        return $product;
    }

    /**
     * Add the date and time of the last update to the product.
     *
     * @since 0.7
     * @param Product $product
     * @param \WP_Post $post
     * @return Product
     */
    private function add_updated_at(Product $product, \WP_Post $post)
    {
        $updated_at = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $post->post_modified);
        $product->set_updated_at($updated_at);

        return $product;
    }

    /**
     * Build the shop from the raw array.
     *
     * @since 0.6
     * @param array $raw_shop
     * @return null|Shop
     */
    private function get_shop_from_array(array $raw_shop)
    {
        $shop_template_id = !empty($raw_shop[self::SHOP_TEMPLATE_ID]) ? intval($raw_shop[self::SHOP_TEMPLATE_ID]) : null;
        if (empty($shop_template_id)) {
            return null;
        }

        $shop_template = $this->shop_template_repository->find_by_id(new Shop_Template_Id($shop_template_id));
        if($shop_template === null) {
            return null;
        }

        $affiliate_link = !empty($raw_shop[self::SHOP_AFFILIATE_LINK]) ? $raw_shop[self::SHOP_AFFILIATE_LINK] : null;
        $affiliate_id = !empty($raw_shop[self::SHOP_AFFILIATE_ID]) ? $raw_shop[self::SHOP_AFFILIATE_ID] : null;
        $availability = !empty($raw_shop[self::SHOP_AVAILABILITY]) ? $raw_shop[self::SHOP_AVAILABILITY] : null;
        $discounted_price = !empty($raw_shop[self::SHOP_DISCOUNTED_PRICE]) ? $raw_shop[self::SHOP_DISCOUNTED_PRICE] : null;
        $stock_price = !empty($raw_shop[self::SHOP_STOCK_PRICE]) ? $raw_shop[self::SHOP_STOCK_PRICE] : null;
        $currency = !empty($raw_shop[self::SHOP_CURRENCY]) ? $raw_shop[self::SHOP_CURRENCY] : null;
        $updated_at = !empty($raw_shop[self::SHOP_UPDATED_AT]) ? $raw_shop[self::SHOP_UPDATED_AT] : null;

        if(empty($affiliate_link) || empty($availability)) {
            return null;
        }

        $shop = $shop_template->build(
            new Tracking(
                new Affiliate_Link($affiliate_link),
                $affiliate_id !== null ? new Affiliate_Id($affiliate_id) : null
            ),
            new Pricing(
                new Availability($availability),
                $discounted_price !== null ? new Money($discounted_price, new Currency($currency)) : null,
                $stock_price !== null ? new Money($stock_price, new Currency($currency)) : null
            )
        );

        $shop->set_template_id(new Shop_Template_Id($shop_template_id));

        if($updated_at !== null) {
            $shop->set_updated_at((new \DateTimeImmutable())->setTimestamp($updated_at));
        }

        return $shop;
    }

    /**
     * Get the type like simple, complex or variants from the raw post.
     * If there is not type stored, the returned type will be default.
     *
     * @since 0.7
     * @param \WP_Post $post
     * @return Type
     */
    private function get_type(\WP_Post $post)
    {
        $type = carbon_get_post_meta($post->ID, self::TYPE);
        $type = !empty($type) ? new Type($type) : Type::simple();

        return $type;
    }

    /**
     * Store the type like simple or variants for the product.
     *
     * @since 0.7
     * @param Product $product
     */
    private function store_type(Product $product)
    {
        $this->store_post_meta($product->get_id()->get_value(), self::TYPE, $product->get_type()->get_value());
    }

    /**
     * Store the shops for the product.
     *
     * @since 0.7
     * @param Product $product
     * @param string $meta_key
     */
    private function store_shops(Product $product, $meta_key)
    {
        if(!($product instanceof Shop_Aware_Interface)) {
            return;
        }

        if(!$product->has_id()) {
            return;
        }

        $shops = $product->get_shops();

        $carbon_shops = array();
        foreach ($shops as $index => $shop) {
            $key = $this->key_generator->generate_from_slug($shop->get_slug());
            if(!isset($carbon_shops[$key->get_value()])) {
                $carbon_shops[$key->get_value()] = array();
            }

            $carbon_shops[$key->get_value()][$index] = array(
                self::SHOP_TEMPLATE_ID => $shop->has_template_id() ? $shop->get_template_id()->get_value() : null,
                self::SHOP_AFFILIATE_LINK => $shop->get_tracking()->get_affiliate_link()->get_value(),
                self::SHOP_AFFILIATE_ID => $shop->get_tracking()->has_affiliate_id() ? $shop->get_tracking()->get_affiliate_id()->get_value() : null,
                self::SHOP_AVAILABILITY => $shop->get_pricing()->get_availability()->get_value(),
                self::SHOP_DISCOUNTED_PRICE => $shop->get_pricing()->has_discounted_price() ? $shop->get_pricing()->get_discounted_price()->get_value() : null,
                self::SHOP_STOCK_PRICE => $shop->get_pricing()->has_stock_price() ? $shop->get_pricing()->get_stock_price()->get_value() : null,
                self::SHOP_CURRENCY => $shop->get_pricing()->has_stock_price() ? $shop->get_pricing()->get_stock_price()->get_currency()->get_value() : Currency::EURO,
                self::SHOP_UPDATED_AT => $shop->get_updated_at()->getTimestamp(),
            );
        }

        $carbon_meta_keys = $this->build_complex_carbon_meta_key($carbon_shops, $meta_key);
        foreach ($carbon_meta_keys as $carbon_meta_key => $carbon_meta_value) {
            if($carbon_meta_value !== null) {
                $this->store_post_meta($product->get_id()->get_value(), $carbon_meta_key, $carbon_meta_value);
            } elseif ($carbon_meta_value === null) {
                $this->delete_post_meta($product->get_id()->get_value(), $carbon_meta_key);
            }
        }
    }

    /**
     * Store the attributes of the product variant.
     *
     * @since 0.7
     * @param Product_Variant $product_variant
     */
    private function store_attributes(Product_Variant $product_variant)
    {
        if(!$product_variant->has_id()) {
            return;
        }

        $attributes = $product_variant->get_attributes();
        if(empty($attributes)) {
            return;
        }

        foreach ($attributes as $attribute) {
            $attribute_key = $this->key_generator->generate_from_slug($attribute->get_slug());
            $meta_key = sprintf(self::ATTRIBUTE_VALUE, $attribute_key->get_value());
            $meta_value = $attribute->get_value()->get_value();

            $this->store_post_meta($product_variant->get_id()->get_value(), $meta_key, $meta_value);
        }
    }

    /**
     * Store the variants for the complex product.
     *
     * @since 0.7
     * @param Complex_Product $complex_product
     */
    private function store_variants(Complex_Product $complex_product)
    {
        if(!$complex_product->has_id()) {
            return;
        }

        $variants = $complex_product->get_variants();
        if(empty($variants)) {
            return;
        }

        /* Example for valid structure:
         *
         * $variants = array(
         *     '_' => array(
         *         0 => array(
         *             'name' => 'test',
         *             'thumbnail' => '',
         *             'shops' => array(
         *                 'amazon' => array(
         *                     0 => array(
         *                        'shop_template_id' => 1234,
         *                        'affiliate_link' => 'http://your-link.com',
         *                        'currency' => 'euro',
         *                        ...
         *                     )
         *                 )
         *             )
         *         ),
         *         ...
         *     )
         * );
         */
        $carbon_variants = array('' => array());
        foreach ($variants as $index => $variant) {
            $shops = $variant->get_shops();
            $carbon_shops = array();
            foreach ($shops as $index2 => $shop) {
                $key = $this->key_generator->generate_from_slug($shop->get_slug());

                if(!isset($carbon_shops[$key->get_value()])) {
                    $carbon_shops[$key->get_value()] = array();
                }

                $carbon_shops[$key->get_value()][$index2] = array(
                    self::SHOP_TEMPLATE_ID => $shop->has_template_id() ? $shop->get_template_id()->get_value() : null,
                    self::SHOP_AFFILIATE_LINK => $shop->get_tracking()->get_affiliate_link()->get_value(),
                    self::SHOP_AFFILIATE_ID => $shop->get_tracking()->has_affiliate_id() ? $shop->get_tracking()->get_affiliate_id()->get_value() : null,
                    self::SHOP_DISCOUNTED_PRICE => $shop->get_pricing()->has_discounted_price() ? $shop->get_pricing()->get_discounted_price()->get_value() : null,
                    self::SHOP_STOCK_PRICE => $shop->get_pricing()->has_stock_price() ? $shop->get_pricing()->get_stock_price()->get_value() : null,
                    self::SHOP_CURRENCY => $shop->get_pricing()->has_discounted_price() ? $shop->get_pricing()->get_discounted_price()->get_currency()->get_value() : Currency::EURO,
                );
            }

            if($variant->has_tags()) {
                $tags = $variant->get_tags();
                $raw_tags = array();
                foreach ($tags as $tag) {
                    $raw_tags[] = $tag->get_value();
                }

                $raw_tags = implode(';', $raw_tags);
            }

            $carbon_variant = array(
                self::VARIANT_ID => $variant->has_id() ? $variant->get_id()->get_value() : null,
                self::VARIANT_NAME => $variant->get_name()->get_value(),
                self::VARIANT_DEFAULT => $variant->is_default() ? 'yes' : null,
                self::VARIANT_TAGS => !empty($raw_tags) ? $raw_tags : null,
                self::VARIANT_THUMBNAIL_ID => $variant->has_thumbnail_id() ? $variant->get_thumbnail_id()->get_value() : null,
                self::VARIANT_SHOPS => !empty($carbon_shops) ? $carbon_shops : null,
            );

            $attributes = $variant->get_attributes();
            foreach ($attributes as $attribute) {
                $key = $this->key_generator->generate_from_slug($attribute->get_slug());
                $carbon_key = sprintf(self::VARIANT_ATTRIBUTE_VALUE, $key->get_value());

                $carbon_variant[$carbon_key] = $attribute->get_value()->get_value();
            }

            $carbon_variants[''][] = $carbon_variant;
        }

        $carbon_meta_keys = $this->build_complex_carbon_meta_key($carbon_variants, self::VARIANTS);
        foreach ($carbon_meta_keys as $carbon_meta_key => $carbon_meta_value) {
            if($carbon_meta_value !== null) {
                $this->store_post_meta($complex_product->get_id()->get_value(), $carbon_meta_key, $carbon_meta_value);
            } elseif ($carbon_meta_value === null) {
                $this->delete_post_meta($complex_product->get_id()->get_value(), $carbon_meta_key);
            }
        }
    }

    /**
     * Store the review for the product.
     *
     * @since 0.7
     * @param Product $product
     */
    private function store_review(Product $product)
    {
        if(!($product instanceof Review_Aware_Interface)) {
            return;
        }

        if($product->has_review()) {
            $this->store_post_meta($product->get_id()->get_value(), self::REVIEW_RATING, $product->get_review()->get_rating()->get_value());

            if($product->get_review()->has_votes()) {
                $this->store_post_meta($product->get_id()->get_value(), self::REVIEW_VOTES, $product->get_review()->get_votes()->get_value());
            }
        }
    }

    /**
     * Store the thumbnail for the product.
     *
     * @since 0.6
     * @param Product $product
     */
    private function store_thumbnail(Product $product)
    {
        if(!$product->has_thumbnail_id()) {
            return;
        }

        $this->store_post_meta($product->get_id()->get_value(), self::THUMBNAIL_ID, $product->get_thumbnail_id()->get_value());
    }

    /**
     * Store the image gallery for the product.
     *
     * @since 0.8
     * @param Product $product
     */
    private function store_image_gallery(Product $product)
    {
        if(!$product->has_image_gallery()) {
            return;
        }

        $images = $product->get_image_gallery();
        $raw_images = array();
        foreach ($images as $image) {
            $raw_images[] = $image->get_value();
        }

        $meta_value = implode(',', $raw_images);

        $this->store_post_meta($product->get_id()->get_value(), self::IMAGE_GALLERY, $meta_value);
    }

    /**
     * Store the tags for the product.
     *
     * @since 0.7.1
     * @param Product $product
     */
    private function store_tags(Product $product)
    {
        if(!($product instanceof Tag_Aware_Interface)) {
            return;
        }

        if(!$product->has_tags()) {
            return;
        }

        $tags = $product->get_tags();
        $raw_tags = array();
        foreach ($tags as $tag) {
            $raw_tags[] = $tag->get_value();
        }

        $raw_tags = implode(';', $raw_tags);
        $this->store_post_meta($product->get_id()->get_value(), self::TAGS, $raw_tags);
    }

    /**
     * Store the details for the product.
     *
     * @since 0.8
     * @param Product $product
     */
    private function store_details(Product $product)
    {
        if(!($product instanceof Detail_Aware_Interface)) {
            return;
        }

        $details = $product->get_details();
        $names = array();
        foreach ($details as $detail) {
            $detail_key = $this->key_generator->generate_from_slug($detail->get_slug());
            $key = sprintf(self::DETAIL_VALUE, $detail_key->get_value());
            $this->store_post_meta($product->get_id(), $key, $detail->get_value()->get_value());
            $names[] = $detail->get_name()->get_value();
        }

        $enabled_details = implode(';', $names);
        $this->store_post_meta($product->get_id()->get_value(), self::ENABLED_DETAILS, $enabled_details);
    }

    /**
     * @inheritdoc
     * @since 0.8
     */
    public function delete_all_variants_from_parent_except($product_variants, Product_Id $parentProduct_Id)
    {
        $not_to_delete = array();
        foreach ($product_variants as $product_variant) {
            if(!($product_variant instanceof Product_Variant)) {
                throw new Invalid_Type_Exception($product_variant, Product_Variant::class);
            }

            if(!$product_variant->get_parent()->has_id() || !$product_variant->has_id()) {
                continue;
            }

            if(!$parentProduct_Id->is_equal_to($product_variant->get_parent()->get_id())) {
                continue;
            }

            $not_to_delete[] = $product_variant->get_id()->get_value();
        }

        $to_delete = array();
        foreach ($product_variants as $product_variant) {
            if($product_variant instanceof Product_Variant) {

                $parent_id = $product_variant->get_parent()->get_id()->get_value();
                if(!isset($to_delete[$parent_id])) {
                    $to_delete[$parent_id] = array();
                }

                $to_delete[$parent_id][] = $product_variant->get_id()->get_value();
            }
        }

        $query = new \WP_Query(array(
            'post_type' => Product::POST_TYPE,
            'post_parent' => $parentProduct_Id->get_value(),
            'post__not_in' => $not_to_delete,
        ));

        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                wp_delete_post($query->post->ID, true);
            }

            wp_reset_postdata();
        }
    }

    /**
     * Build the default args from the saved product in the database.
     *
     * @since 0.6
     * @param Product $product
     * @return array
     */
    private function get_default_args(Product $product)
    {
        $default_args = array();
        if($product->has_id()) {
            $default_args = get_post($product->get_id()->get_value(), ARRAY_A);
        }

        return $default_args;
    }

    /**
     * Build the args to save the product.
     *
     * @since 0.6
     * @param Product $product
     * @param array $default_args
     * @return array
     */
    private function parse_args(Product $product, array $default_args = array())
    {
        $args = wp_parse_args(array(
            'post_title' => $product->get_name()->get_value(),
            'post_status' => 'publish',
            'post_name' => $product->get_slug()->get_value(),
            'post_type' => Product::POST_TYPE,
            'post_modified' => date('Y-m-d H:i:s', $product->get_updated_at()->getTimestamp()),
            'post_modified_gmt' => gmdate('Y-m-d H:i:s', $product->get_updated_at()->getTimestamp()),
        ), $default_args);

        if($product->has_id()) {
            $args['id'] = $product->get_id()->get_value();
        }

        if($product instanceof Content_Aware_Interface && $product->has_content()) {
            $args['post_content'] = $product->get_content()->get_value();
        }

        if($product instanceof Excerpt_Aware_Interface && $product->has_excerpt()) {
            $args['post_excerpt'] = $product->get_excerpt()->get_value();
        }

        if($product instanceof Product_Variant) {
            $args['post_parent'] = $product->get_parent()->get_id()->get_value();
        }

        return $args;
    }
}
