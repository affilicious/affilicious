<?php
namespace Affilicious\Product\Infrastructure\Repository\Carbon;

use Affilicious\Attribute\Domain\Model\Attribute_Group;
use Affilicious\Attribute\Domain\Model\Attribute_Group_Factory_Interface;
use Affilicious\Attribute\Domain\Model\Attribute_Template_Group_Id;
use Affilicious\Common\Domain\Exception\Invalid_Post_Type_Exception;
use Affilicious\Common\Domain\Exception\Invalid_Type_Exception;
use Affilicious\Common\Domain\Model\Content;
use Affilicious\Common\Domain\Model\Excerpt;
use Affilicious\Common\Domain\Model\Image\Height;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Image\Image_Id;
use Affilicious\Common\Domain\Model\Image\Source;
use Affilicious\Common\Domain\Model\Image\Width;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Common\Infrastructure\Repository\Carbon\Abstract_Carbon_Repository;
use Affilicious\Detail\Domain\Model\Detail\Detail;
use Affilicious\Detail\Domain\Model\Detail\Unit;
use Affilicious\Detail\Domain\Model\Detail\Value;
use Affilicious\Detail\Domain\Model\Detail_Group;
use Affilicious\Detail\Domain\Model\Detail_Group_Factory_Interface;
use Affilicious\Detail\Domain\Model\Detail_Template_Group_Id;
use Affilicious\Product\Domain\Exception\Failed_To_Delete_Product_Exception;
use Affilicious\Product\Domain\Exception\Missing_Parent_Product_Exception;
use Affilicious\Product\Domain\Exception\Product_Not_Found_Exception;
use Affilicious\Product\Domain\Model\Complex\Complex_Product;
use Affilicious\Product\Domain\Model\Complex\Complex_Product_Interface;
use Affilicious\Product\Domain\Model\Product_Id;
use Affilicious\Product\Domain\Model\Product_Interface;
use Affilicious\Product\Domain\Model\Product_Repository_Interface;
use Affilicious\Product\Domain\Model\Review\Rating;
use Affilicious\Product\Domain\Model\Review\Review_Factory_Interface;
use Affilicious\Product\Domain\Model\Review\Votes;
use Affilicious\Product\Domain\Model\Simple\Simple_Product;
use Affilicious\Product\Domain\Model\Simple\Simple_Product_Interface;
use Affilicious\Product\Domain\Model\Type;
use Affilicious\Product\Domain\Model\Variant\Product_Variant;
use Affilicious\Product\Domain\Model\Variant\Product_Variant_Interface;
use Affilicious\Shop\Domain\Model\Shop;
use Affilicious\Shop\Domain\Model\Shop_Factory_Interface;
use Affilicious\Shop\Domain\Model\Shop_Template_Id;
use Affilicious\Shop\Domain\Model\Shop_Template_Repository_Interface;
use Affilicious\Detail\Domain\Model\Detail\Type as DetailType;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Carbon_Product_Repository extends Abstract_Carbon_Repository implements Product_Repository_Interface
{
    const TYPE = 'affilicious_product_type';
    const IMAGE_GALLERY = '_affilicious_product_image_gallery';

    const SHOPS = 'affilicious_product_shops';
    const SHOP_TEMPLATE_ID = 'shop_template_id';
    const SHOP_PRICE = 'price';
    const SHOP_OLD_PRICE = 'old_price';
    const SHOP_CURRENCY = 'currency';
    const SHOP_AFFILIATE_ID = 'affiliate_id';
    const SHOP_AFFILIATE_LINK = 'affiliate_link';
    const SHOP_UPDATED_AT = 'updated_at';

    const DETAIL_GROUPS = 'affilicious_product_detail_groups';
    const DETAIL_TEMPLATE_GROUP_ID = 'affilicious_product_detail_template_group_id';

    const ATTRIBUTE_GROUP_KEY = 'affilicious_product_attribute_group_key';
    const ATTRIBUTE_GROUPS = 'affilicious_product_attribute_groups';
    const ATTRIBUTE_GROUP_TEMPLATE_ID = 'template_id';
    const ATTRIBUTE_GROUP_ATTRIBUTE = 'attribute';

    const VARIANTS = 'affilicious_product_variants';
    const VARIANT_ID = 'variant_id';
    const VARIANT_TITLE = 'title';
    const VARIANT_DEFAULT = 'default';
    const VARIANT_ATTRIBUTE_TEMPLATE_GROUP_ID = 'template_group_id';
    const VARIANT_ATTRIBUTE = 'attribute';
    const VARIANT_THUMBNAIL = 'thumbnail';
    const VARIANT_SHOPS = 'shops';

    const REVIEW_RATING = 'affilicious_product_review_rating';
    const REVIEW_VOTES = 'affilicious_product_review_votes';

    const RELATED_PRODUCTS = 'affilicious_product_related_products';
    const RELATED_ACCESSORIES = 'affilicious_product_related_accessories';

    // TODO: Remove the legacy support in the beta
    const SHOP_ID = 'shop_id';
    const DETAIL_GROUP_ID = 'detail_group_id';

    /**
     * @var Review_Factory_Interface
     */
    protected $review_factory;

    /**
     * @var Detail_Group_Factory_Interface
     */
    protected $detail_group_factory;

    /**
     * @var Attribute_Group_Factory_Interface
     */
    protected $attribute_group_factory;

    /**
     * @var Shop_Template_Repository_Interface
     */
    protected $shop_factory;

    /**
     * @since 0.6
     * @param Review_Factory_Interface $review_factory
     * @param Detail_Group_Factory_Interface $detail_group_factory
     * @param Attribute_Group_Factory_Interface $attribute_group_factory ,
     * @param Shop_Factory_Interface $shop_factory
     */
    public function __construct(
        Review_Factory_Interface $review_factory,
        Detail_Group_Factory_Interface $detail_group_factory,
        Attribute_Group_Factory_Interface $attribute_group_factory,
        Shop_Factory_Interface $shop_factory
    )
    {
        $this->review_factory = $review_factory;
        $this->detail_group_factory = $detail_group_factory;
        $this->attribute_group_factory = $attribute_group_factory;
        $this->shop_factory = $shop_factory;
    }

    /**
     * @inheritdoc
     * @since 0.7
     */
    public function store(Product_Interface $product)
    {
        // Product variants must have a parent product
        if($product instanceof Product_Variant_Interface && !$product->get_parent()->has_id()) {
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
            $product->set_name(new Name($post->post_name));
        }

        // Store the product meta
        $this->store_type($product);

        $this->store_thumbnail($product);

        if(!($product instanceof Complex_Product_Interface)) {
            $this->store_shops($product, self::SHOPS);
        }

        if(!($product instanceof Product_Variant_Interface)) {
            $this->store_review($product);
        }

        if($product instanceof Product_Variant_Interface){
            $this->store_attribute_group($product);
        }

        if($product instanceof Complex_Product_Interface){
            $this->store_variants($product);
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

        if($post->post_type != Product_Interface::POST_TYPE) {
            throw new Invalid_Post_Type_Exception($post->post_type, Product_Interface::POST_TYPE);
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
            if($product instanceof Product_Interface && $product->has_id()) {
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
            'post_type' => Product_Interface::POST_TYPE,
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
     * @return Product_Interface
     */
    protected function build_product_from_post(\WP_Post $post)
    {
        if($post->post_type !== Product_Interface::POST_TYPE) {
            throw new Invalid_Post_Type_Exception($post->post_type, Product_Interface::POST_TYPE);
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
     * @return Simple_Product_Interface
     */
    protected function build_simple_product_from_post(\WP_Post $post)
    {
        if($post->post_type !== Product_Interface::POST_TYPE) {
            throw new Invalid_Post_Type_Exception($post->post_type, Product_Interface::POST_TYPE);
        }

        $title = new Title($post->post_title);
        $name = new Name($post->post_name);
        $key = $name->to_key();

        $simple_product = new Simple_Product($title, $name, $key);
        $simple_product = $this->add_id($simple_product, $post);
        $simple_product = $this->add_content($simple_product, $post);
        $simple_product = $this->add_excerpt($simple_product, $post);
        $simple_product = $this->add_thumbnail($simple_product, $post);
        $simple_product = $this->add_shops($simple_product);
        $simple_product = $this->add_detail_groups($simple_product, $post);
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
     * @return Complex_Product_Interface
     */
    protected function build_complex_product_from_post(\WP_Post $post)
    {
        if($post->post_type !== Product_Interface::POST_TYPE) {
            throw new Invalid_Post_Type_Exception($post->post_type, Product_Interface::POST_TYPE);
        }

        $title = new Title($post->post_title);
        $name = new Name($post->post_name);
        $key = $name->to_key();

        $complex_product = new Complex_Product($title, $name, $key);
        $complex_product = $this->add_id($complex_product, $post);
        $complex_product = $this->add_content($complex_product, $post);
        $complex_product = $this->add_excerpt($complex_product, $post);
        $complex_product = $this->add_thumbnail($complex_product, $post);
        $complex_product = $this->add_variants($complex_product);
        $complex_product = $this->add_detail_groups($complex_product, $post);
        $complex_product = $this->add_review($complex_product, $post);
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
     * @param Complex_Product_Interface $parent
     * @return Product_Variant_Interface
     */
    protected function build_product_variant_from_post(\WP_Post $post, Complex_Product_Interface $parent = null)
    {
        if($post->post_type !== Product_Interface::POST_TYPE) {
            throw new Invalid_Post_Type_Exception($post->post_type, Product_Interface::POST_TYPE);
        }

        if($parent === null) {
            $parent = $this->get_parent_complex_product(new Product_Id($post->ID));
        }

        $title = new Title($post->post_title);
        $name = new Name($post->post_name);
        $key = $name->to_key();

        $product_variant = new Product_Variant($parent, $title, $name, $key);
        $product_variant = $this->add_id($product_variant, $post);
        $product_variant = $this->add_thumbnail($product_variant, $post);
        $product_variant = $this->add_attribute_group($product_variant, $post);
        $product_variant = $this->add_shops($product_variant);
        $product_variant = $this->add_updated_at($product_variant, $post);

        return $product_variant;
    }

    /**
     * Find the parent complex product of the product variant by the given ID.
     *
     * @since 0.6
     * @param Product_Id $product_variant_id
     * @return null|Complex_Product_Interface
     */
    protected function get_parent_complex_product(Product_Id $product_variant_id)
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
     * @param Product_Interface $product
     * @param \WP_Post $post
     * @return Product_Interface
     */
    protected function add_id(Product_Interface $product, \WP_Post $post)
    {
        $product->set_id(new Product_Id($post->ID));

        return $product;
    }

    /**
     * Add the thumbnail to the product.
     *
     * @since 0.7
     * @param Product_Interface $product
     * @param \WP_Post $post
     * @return Product_Interface
     */
    protected function add_thumbnail(Product_Interface $product, \WP_Post $post)
    {
        $thumbnail_id = get_post_thumbnail_id($post->ID);
        if (!empty($thumbnail_id)) {
            $thumbnail = self::get_image_from_attachment_id($thumbnail_id);

            if($thumbnail !== null) {
                $product->set_thumbnail($thumbnail);
            }
        }

        return $product;
    }

    /**
     * Add the excerpt to the product.
     *
     * @since 0.7
     * @param Product_Interface $product
     * @param \WP_Post $post
     * @return Product_Interface
     */
    protected function add_excerpt(Product_Interface $product, \WP_Post $post)
    {
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
     * @param Product_Interface $product
     * @param \WP_Post $post
     * @return Product_Interface
     */
    protected function add_content(Product_Interface $product, \WP_Post $post)
    {
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
     * @param Product_Interface $product
     * @param array $raw_shops
     * @return Product_Interface
     */
    protected function add_shops(Product_Interface $product, $raw_shops = array())
    {
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
     * @param Complex_Product_Interface $complex_product
     * @return Complex_Product_Interface
     */
    protected function add_variants(Complex_Product_Interface $complex_product)
    {
        $raw_variants = carbon_get_post_meta($complex_product->get_id()->get_value(), self::VARIANTS, 'complex');

        foreach ($raw_variants as $raw_variant)
        {
            $id = !empty($raw_variant[self::VARIANT_ID]) ? $raw_variant[self::VARIANT_ID] : null;
            $title = !empty($raw_variant[self::VARIANT_TITLE]) ? $raw_variant[self::VARIANT_TITLE] : null;
            $thumbnail_id = !empty($raw_variant[self::VARIANT_THUMBNAIL]) ? $raw_variant[self::VARIANT_THUMBNAIL] : null;
            $shops = !empty($raw_variant[self::VARIANT_SHOPS]) ? $raw_variant[self::VARIANT_SHOPS] : null;
            $default = !empty($raw_variant[self::VARIANT_DEFAULT]) ? $raw_variant[self::VARIANT_DEFAULT] : null;
            $attribute_template_group_id = !empty($raw_variant[self::VARIANT_ATTRIBUTE_TEMPLATE_GROUP_ID]) ? $raw_variant[self::VARIANT_ATTRIBUTE_TEMPLATE_GROUP_ID] : null;
            $attribute_group = $this->get_attribute_group_from_id_and_array($attribute_template_group_id, $raw_variant);

            if(empty($title) || empty($attribute_group)) {
                continue;
            }

            $title = new Title($title);
            $name = $title->to_name();
            $key = $name->to_key();
            $product_variant = new Product_Variant(
                $complex_product,
                $title,
                $name,
                $key
            );

            $product_variant->set_attribute_group($attribute_group);

            if(!empty($id)) {
                $product_variant->set_id(new Product_Id($id));
            }

            $thumbnail = $this->get_image_from_attachment_id($thumbnail_id);
            if(!empty($thumbnail)) {
                $product_variant->set_thumbnail($thumbnail);
            }

            if(!empty($default) && $default === 'yes') {
                $product_variant->set_default(true);
            }

            if(!empty($shops)) {
                $this->add_shops($product_variant, $shops);
            }

            $complex_product->add_variant($product_variant);
        }

        return $complex_product;
    }

    /**
     * Add detail groups to the product.
     *
     * @since 0.7
     * @param Product_Interface $product
     * @param \WP_Post $post
     * @return Product_Interface
     */
    protected function add_detail_groups(Product_Interface $product, \WP_Post $post)
    {
        // Add the default detail group with the price as a detail
        $cheapest_shop = $product->get_cheapest_shop();
        if($cheapest_shop !== null && $cheapest_shop->has_price()) {
            $default_detail_group = $this->detail_group_factory->create(
                new Title(__('Default')),
                new Name('default'),
                new Key('default')
            );

            $price_detail = new Detail(
                new Title(__('Price', 'affilicious')),
                new Name('price'),
                new Key('price'),
                DetailType::number()
            );

            $price_detail->set_unit(new Unit($cheapest_shop->get_price()->get_currency()->get_symbol()));
            $price_detail->set_value(new Value($cheapest_shop->get_price()->get_value()));

            $default_detail_group->add_detail($price_detail);
            $product->add_detail_group($default_detail_group);
        }

        $raw_detail_groups = carbon_get_post_meta($post->ID, self::DETAIL_GROUPS, 'complex');
        if (!empty($raw_detail_groups)) {
            foreach ($raw_detail_groups as $raw_detail_group) {
                $detail_group = self::get_detail_group_from_array($raw_detail_group);

                if (!empty($detail_group)) {
                    $product->add_detail_group($detail_group);
                }
            }
        }

        return $product;
    }

    /**
     * Add the attribute group to the product variant.
     *
     * @since 0.7
     * @param Product_Variant_Interface $product_variant
     * @param \WP_Post $post
     * @return Product_Variant_Interface
     */
    protected function add_attribute_group(Product_Variant_Interface $product_variant, \WP_Post $post)
    {
        // Find the attribute groups
        $raw_attribute_groups = carbon_get_post_meta($post->ID, self::ATTRIBUTE_GROUPS, 'complex');
        if(empty($raw_attribute_groups)) {
            return $product_variant;
        }

        // There is always just one group inside the array
        $raw_attribute_group = $raw_attribute_groups[0];
        $raw_template_id = $raw_attribute_group[self::ATTRIBUTE_GROUP_TEMPLATE_ID];

        $attribute_group = $this->get_attribute_group_from_id_and_array($raw_template_id, $raw_attribute_group);
        if(empty($attribute_group)) {
            return $product_variant;
        }

        $product_variant->set_attribute_group($attribute_group);

        return $product_variant;
    }

    /**
     * Add the review to the product.
     *
     * @since 0.7
     * @param Product_Interface $product
     * @param \WP_Post $post
     * @return Product_Interface
     */
    protected function add_review(Product_Interface $product, \WP_Post $post)
    {
        $rating = carbon_get_post_meta($post->ID, self::REVIEW_RATING);
        if((!empty($rating) || $rating == '0') && $rating !== 'none') {
            $review = $this->review_factory->create(new Rating($rating));

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
     * @param Product_Interface $product
     * @param \WP_Post $post
     * @return Product_Interface
     */
    protected function add_related_products(Product_Interface $product, \WP_Post $post)
    {
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
     * @param Product_Interface $product
     * @param \WP_Post $post
     * @return Product_Interface
     */
    protected function add_related_accessories(Product_Interface $product, \WP_Post $post)
    {
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
     * @param Product_Interface $product
     * @param \WP_Post $post
     * @return Product_Interface
     */
    protected function add_image_gallery(Product_Interface $product, \WP_Post $post)
    {
        if(!($product instanceof Simple_Product_Interface) || !($product instanceof Complex_Product_Interface)) {
            return $product;
        }

        $image_gallery = get_post_meta($post->ID, self::IMAGE_GALLERY);
        if (!empty($image_gallery)) {
            $image_ids = explode(',', $image_gallery[0]);

            $images = array();
            foreach ($image_ids as $image_id) {
                $image = self::get_image_from_attachment_id($image_id);

                if($image !== null) {
                    $images[] = $image;
                }
            }

            $product->set_image_gallery($images);
        }

        return $product;
    }


    /**
     * Add the date and time of the last update to the product.
     *
     * @since 0.7
     * @param Product_Interface $product
     * @param \WP_Post $post
     * @return Product_Interface
     */
    protected function add_updated_at(Product_Interface $product, \WP_Post $post)
    {
        $updated_at = \DateTime::createFromFormat('Y-m-d H:i:s', $post->post_modified);
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
    protected function get_shop_from_array(array $raw_shop)
    {
        $shop_template_id = !empty($raw_shop[self::SHOP_TEMPLATE_ID]) ? intval($raw_shop[self::SHOP_TEMPLATE_ID]) : null;

        // TODO: Remove the legacy support in the beta
        if(empty($shop_template_id)) {
            $shop_template_id = !empty($raw_shop[self::SHOP_ID]) ? intval($raw_shop[self::SHOP_ID]) : null;
        }

        if (empty($shop_template_id)) {
            return null;
        }

        $shop = $this->shop_factory->create_from_template_id_and_data(
            new Shop_Template_Id($shop_template_id),
            $raw_shop
        );

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
    protected function get_type(\WP_Post $post)
    {
        $type = carbon_get_post_meta($post->ID, self::TYPE);
        $type = !empty($type) ? new Type($type) : Type::simple();

        return $type;
    }

    /**
     * Build the detail group from the raw array.
     *
     * @since 0.6
     * @param array $raw_detail_group
     * @return null|Detail_group
     */
    protected function get_detail_group_from_array(array $raw_detail_group)
    {
        $detail_template_group_id = !empty($raw_detail_group[self::DETAIL_TEMPLATE_GROUP_ID]) ? intval($raw_detail_group[self::DETAIL_TEMPLATE_GROUP_ID]) : null;

        // TODO: Remove the legacy support in the beta
        if(empty($detail_template_group_id)) {
            $detail_template_group_id = !empty($raw_detail_group[self::DETAIL_GROUP_ID]) ? intval($raw_detail_group[self::DETAIL_GROUP_ID]) : null;
        }

        if (empty($detail_template_group_id)) {
            return null;
        }

        $detail_group = $this->detail_group_factory->create_from_template_id_and_data(
            new Detail_Template_Group_Id($detail_template_group_id),
            $raw_detail_group
        );

        return $detail_group;
    }

    /**
     * Build the attribute group from the raw array.
     *
     * @since 0.6
     * @param int $attribute_template_group_id
     * @param array $raw_variant
     * @return Attribute_Group|null
     */
    protected function get_attribute_group_from_id_and_array($attribute_template_group_id, $raw_variant)
    {
        if (empty($attribute_template_group_id) || empty($raw_variant)) {
            return null;
        }

        $attribute_group = $this->attribute_group_factory->create_from_template_id_and_data(
            new Attribute_Template_Group_Id($attribute_template_group_id),
            $raw_variant
        );

        return $attribute_group;
    }

    /**
     * Build the image object from the array.
     *
     * @since 0.6
     * @param int $attachment_id
     * @return null|Image
     */
    protected function get_image_from_attachment_id($attachment_id)
    {
        $attachment = wp_get_attachment_image_src($attachment_id);
        if(empty($attachment) && count($attachment) == 0) {
            return null;
        }

        $source = $attachment[0];
        if(empty($source)) {
            return null;
        }

        $image = new Image(
            new Image_Id($attachment_id),
            new Source($source)
        );

        $width = $attachment[1];
        if(!empty($width)) {
            $image->set_width(new Width($width));
        }

        $height = $attachment[2];
        if(!empty($height)) {
            $image->set_height(new Height($height));
        }

        return $image;
    }

    /**
     * Get the thumbnail image from the post.
     *
     * @since 0.6
     * @param int $post_id
     * @return null|Image
     */
    protected function get_thumbnail_image_from_post_id($post_id)
    {
        $thumbnail_id = get_post_thumbnail_id($post_id);
        if (!empty($thumbnail_id)) {
            $thumbnail = self::get_image_from_attachment_id($thumbnail_id);

            return $thumbnail;
        }

        return null;
    }

    /**
     * Store the type like simple or variants for the product.
     *
     * @since 0.7
     * @param Product_Interface $product
     */
    protected function store_type(Product_Interface $product)
    {
        $this->store_post_meta($product->get_id(), self::TYPE, $product->get_type());
    }

    /**
     * Store the shops for the product.
     *
     * @since 0.7
     * @param Product_Interface $product
     * @param string $meta_key
     */
    protected function store_shops(Product_Interface $product, $meta_key)
    {
        if(!$product->has_id()) {
            return;
        }

        $shops = $product->get_shops();

        $carbon_shops = array();
        foreach ($shops as $index => $shop) {
            if(!isset($carbon_shops[$shop->get_template()->get_key()->get_value()])) {
                $carbon_shops[$shop->get_template()->get_key()->get_value()] = array();
            }

            $carbon_shops[$shop->get_template()->get_key()->get_value()][$index] = array(
                self::SHOP_TEMPLATE_ID => $shop->get_template()->get_id()->get_value(),
                self::SHOP_AFFILIATE_ID => $shop->has_affiliate_id() ? $shop->get_affiliate_id()->get_value() : null,
                self::SHOP_AFFILIATE_LINK => $shop->get_affiliate_link()->get_value(),
                self::SHOP_PRICE => $shop->has_price() ? $shop->get_price()->get_value() : null,
                self::SHOP_CURRENCY => $shop->get_currency()->get_value(),
                self::SHOP_OLD_PRICE => $shop->has_old_price() ? $shop->get_old_price()->get_value() : null,
                self::SHOP_UPDATED_AT => $shop->get_updated_at()->format('Y-m-d H:i:s'),
            );
        }

        $carbon_meta_keys = $this->build_complex_carbon_meta_key($carbon_shops, $meta_key);
        foreach ($carbon_meta_keys as $carbon_meta_key => $carbon_meta_value) {
            if($carbon_meta_value !== null) {
                $this->store_post_meta($product->get_id(), $carbon_meta_key, $carbon_meta_value);
            } elseif ($carbon_meta_value === null) {
                $this->delete_post_meta($product->get_id(), $carbon_meta_key);
            }
        }
    }

    /**
     * Store the attribute group of the product.
     *
     * @since 0.7
     * @param Product_Variant_Interface $product_variant
     */
    protected function store_attribute_group(Product_Variant_Interface $product_variant)
    {
        if(!$product_variant->has_id()) {
            return;
        }

        $attribute_group = $product_variant->get_attribute_group();
        if($attribute_group === null) {
            return;
        }

        $attributes = $attribute_group->get_attributes();

        $carbon_attributes = array();
        $carbon_attributes[0] = array(
            self::ATTRIBUTE_GROUP_TEMPLATE_ID => $attribute_group->has_template_id() ? $attribute_group->get_template_id()->get_value() : null,
        );

        foreach ($attributes as $attribute) {
            $carbon_key = self::ATTRIBUTE_GROUP_ATTRIBUTE . '_' .  $attribute->get_key()->get_value();
            $carbon_attributes[0][$carbon_key] = $attribute->get_value()->get_value();
        }

        $carbon_attribute_group = array(
            $attribute_group->get_key()->get_value() => $carbon_attributes
        );

        $carbon_meta_keys = $this->build_complex_carbon_meta_key($carbon_attribute_group, self::ATTRIBUTE_GROUPS);
        foreach ($carbon_meta_keys as $carbon_meta_key => $carbon_meta_value) {
            if($carbon_meta_value !== null) {
                $this->store_post_meta($product_variant->get_id(), $carbon_meta_key, $carbon_meta_value);
            } elseif ($carbon_meta_value === null) {
                $this->delete_post_meta($product_variant->get_id(), $carbon_meta_key);
            }
        }
    }

    /**
     * Store the variants for the complex product.
     *
     * @since 0.7
     * @param Complex_Product_Interface $complex_product
     */
    protected function store_variants(Complex_Product_Interface $complex_product)
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
         *             'title' => 'test',
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
        $carbon_variants = array('_' => array());
        foreach ($variants as $index => $variant) {

            if(!$variant->has_attribute_group()) {
                continue;
            }

            $shops = $variant->get_shops();
            $carbon_shops = array();
            foreach ($shops as $shop) {
                if(!isset($carbon_shops[$shop->get_template()->get_key()->get_value()])) {
                    $carbon_shops[$shop->get_template()->get_key()->get_value()] = array();
                }

                $carbon_shops[$shop->get_template()->get_key()->get_value()][$index] = array(
                    self::SHOP_TEMPLATE_ID => $shop->get_template()->get_id()->get_value(),
                    self::SHOP_AFFILIATE_ID => $shop->has_affiliate_id() ? $shop->get_affiliate_id()->get_value() : null,
                    self::SHOP_AFFILIATE_LINK => $shop->get_affiliate_link()->get_value(),
                    self::SHOP_CURRENCY => $shop->get_currency()->get_value(),
                    self::SHOP_PRICE => $shop->has_price() ? $shop->get_price()->get_value() : null,
                    self::SHOP_OLD_PRICE => $shop->has_old_price() ? $shop->get_old_price()->get_value() : null,
                );
            }

            $carbon_variants[$variant->get_attribute_group()->get_key()->get_value()][] = array(
                'default' => $variant->is_default() ? 'yes' : null,
                'variant_id' => $variant->has_id() ? $variant->get_id()->get_value() : null,
                'title' => $variant->get_title()->get_value(),
                'thumbnail' => $variant->has_thumbnail() ? $variant->get_thumbnail()->get_id()->get_value() : null,
                'shops' => !empty($carbon_shops) ? $carbon_shops : null,
            );
        }

        $carbon_meta_keys = $this->build_complex_carbon_meta_key($carbon_variants, self::VARIANTS);
        foreach ($carbon_meta_keys as $carbon_meta_key => $carbon_meta_value) {
            if($carbon_meta_value !== null) {
                $this->store_post_meta($complex_product->get_id(), $carbon_meta_key, $carbon_meta_value);
            } elseif ($carbon_meta_value === null) {
                $this->delete_post_meta($complex_product->get_id(), $carbon_meta_key);
            }
        }
    }

    /**
     * Store the review for the product.
     *
     * @since 0.7
     * @param Product_Interface $product
     */
    protected function store_review(Product_Interface $product)
    {
        if($product->has_review()) {
            $this->store_post_meta($product->get_id(), self::REVIEW_RATING, $product->get_review()->get_rating());

            if($product->get_review()->has_votes()) {
                $this->store_post_meta($product->get_id(), self::REVIEW_VOTES, $product->get_review()->get_votes());
            }
        }
    }

    /**
     * Store the thumbnail for the product.
     *
     * @since 0.6
     * @param Product_Interface $product
     */
    protected function store_thumbnail(Product_Interface $product)
    {
        if(!$product->has_thumbnail()) {
            return;
        }

        $this->store_post_meta($product->get_id(), self::THUMBNAIL_ID, $product->get_thumbnail()->get_id());
    }

    /**
     * @inheritdoc
     */
    public function delete_all_variants_from_parent_except($product_variants, Product_Id $parentProduct_Id)
    {
        $not_to_delete = array();
        foreach ($product_variants as $product_variant) {
            if(!($product_variant instanceof Product_Variant)) {
                throw new Invalid_Type_Exception($product_variant, 'Affilicious\Product\Domain\Model\Variant\Product_Variant');
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
            'post_type' => Product_Interface::POST_TYPE,
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
     * @param Product_Interface $product
     * @return array
     */
    protected function get_default_args(Product_Interface $product)
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
     * @param Product_Interface $product
     * @param array $default_args
     * @return array
     */
    protected function parse_args(Product_Interface $product, array $default_args = array())
    {
        $args = wp_parse_args(array(
            'post_title' => $product->get_title()->get_value(),
            'post_status' => 'publish',
            'post_name' => $product->get_name()->get_value(),
            'post_type' => Product_Interface::POST_TYPE,
            'post_modified' => date('Y-m-d H:i:s', $product->get_updated_at()->getTimestamp()),
            'post_modified_gmt' => gmdate('Y-m-d H:i:s', $product->get_updated_at()->getTimestamp()),
        ), $default_args);

        if($product->has_id()) {
            $args['id'] = $product->get_id()->get_value();
        }

        if($product->has_content()) {
            $args['post_content'] = $product->get_content()->get_value();
        }

        if($product->has_excerpt()) {
            $args['post_excerpt'] = $product->get_excerpt()->get_value();
        }

        if($product instanceof Product_Variant) {
            $args['post_parent'] = $product->get_parent()->get_id()->get_value();
        }

        return $args;
    }
}
