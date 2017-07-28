<?php
namespace Affilicious\Product\Helper;

use Affilicious\Attribute\Factory\Attribute_Template_Factory_Interface;
use Affilicious\Attribute\Model\Attribute;
use Affilicious\Attribute\Model\Type;
use Affilicious\Attribute\Model\Value;
use Affilicious\Attribute\Repository\Attribute_Template_Repository_Interface;
use Affilicious\Common\Generator\Slug_Generator_Interface;
use Affilicious\Common\Helper\Image_Helper;
use Affilicious\Common\Model\Image;
use Affilicious\Common\Model\Image_Id;
use Affilicious\Common\Model\Name;
use Affilicious\Common\Model\Slug;
use Affilicious\Product\Model\Complex_Product;
use Affilicious\Product\Model\Product;
use Affilicious\Product\Model\Product_Variant;
use Affilicious\Product\Model\Shop_Aware_Interface;
use Affilicious\Product\Model\Simple_Product;
use Affilicious\Provider\Repository\Provider_Repository_Interface;
use Affilicious\Shop\Factory\Shop_Template_Factory_Interface;
use Affilicious\Shop\Model\Affiliate_Link;
use Affilicious\Shop\Model\Affiliate_Product_Id;
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

class Amazon_Helper
{
    /**
     * Find and attach the thumbnail located in the Amazon API item response to the product.
     *
     * @since 0.9
     * @param array $item The Amazon API response converted from XML to an array.
     * @param bool $create_missing
     * @return Image_Id|null
     */
    public static function find_thumbnail(array $item, $create_missing = true)
    {
        if(isset($item['LargeImage'])) {
            $url = $item['LargeImage']['URL'];
        } elseif(isset($item['MediumImage'])) {
            $url = $item['MediumImage']['URL'];
        } else if(isset($item['SmallImage'])) {
            $url = $item['SmallImage']['URL'];
        } else {
            return null;
        }

        if($create_missing) {
            $thumbnail = Image_Helper::download($url);
        } else {
            $thumbnail = new Image(null, $url);
        }

        $thumbnail = apply_filters('aff_amazon_helper_find_thumbnail', $thumbnail, $item);

        return $thumbnail;
    }

    /**
     * Find and attach the image gallery located in the Amazon API item response to the product.
     *
     * @since 0.9
     * @param array $item
     * @param bool $create_missing
     * @return Image_Id[]
     */
    public static function find_image_gallery(array $item, $create_missing = true)
    {
        $image_gallery = [];

        if(isset($item['ImageSets']['ImageSet'])) {
            $images = $item['ImageSets']['ImageSet'];
            foreach ($images as $image) {
                if(isset($item['LargeImage'])) {
                    $url = $image['LargeImage']['URL'];
                } elseif(isset($image['MediumImage'])) {
                    $url = $image['MediumImage']['URL'];
                } else if(isset($image['SmallImage'])) {
                    $url = $image['SmallImage']['URL'];
                } else {
                    continue;
                }

                if($create_missing) {
                    $image = Image_Helper::download($url);
                } else {
                    $image = new Image(null, $url);
                }

                if($image === null) {
                    continue;
                }

                $image_gallery[] = $image;
            }
        }

        $image_gallery = apply_filters('aff_amazon_helper_find_image_gallery', $image_gallery, $item);

        return $image_gallery;
    }

    /**
     * Find the tracking containing the affiliate link and product ID in the Amazon API item response.
     *
     * @since 0.9
     * @param array $item The Amazon API response converted from XML to an array.
     * @return null|Tracking
     */
    public static function find_tracking(array $item)
    {
        $tracking = null;
        $affiliate_link = self::find_affiliate_link($item);
        $affiliate_product_id = self::find_affiliate_product_id($item);

        if($affiliate_link !== null) {
            $tracking = new Tracking($affiliate_link, $affiliate_product_id);
        }

        $tracking = apply_filters('aff_amazon_helper_find_tracking', $tracking, $item);

        return $tracking;
    }

    /**
     * Find the pricing containing the availability, price and old price in the Amazon API item response.
     *
     * @since 0.9
     * @param array $item The Amazon API response converted from XML to an array.
     * @return null|Pricing
     */
    public static function find_pricing(array $item)
    {
        $pricing = null;
        $availability = self::find_availability($item);
        $price = self::find_price($item);
        $old_price = self::find_old_price($item);

        if($availability !== null) {
            $pricing = new Pricing($availability, $price, $old_price);
        }

        $pricing = apply_filters('aff_amazon_helper_find_pricing', $pricing, $item);

        return $pricing;
    }

    /**
     * Find the affiliate link in the Amazon API item response.
     *
     * @since 0.9
     * @param array $item The Amazon API response converted from XML to an array.
     * @return null|Affiliate_Link
     */
    public static function find_affiliate_link(array $item)
    {
        $affiliate_link = null;

        if(isset($item['DetailPageURL'])) {
            $affiliate_link = new Affiliate_Link($item['DetailPageURL']);
        }

        $affiliate_link = apply_filters('aff_amazon_helper_find_affiliate_link', $affiliate_link, $item);

        return $affiliate_link;
    }

    /**
     * Find the affiliate product ID in the Amazon API item response.
     *
     * @since 0.9
     * @param array $item The Amazon API response converted from XML to an array.
     * @return null|Affiliate_Product_Id
     */
    public static function find_affiliate_product_id(array $item)
    {
        $affiliate_product_id = null;

        if(isset($item['ASIN'])) {
            $affiliate_product_id = new Affiliate_Product_Id($item['ASIN']);
        }

        $affiliate_product_id = apply_filters('aff_amazon_helper_find_affiliate_product_id', $affiliate_product_id, $item);

        return $affiliate_product_id;
    }

    /**
     * Find the availability in the Amazon API item response..
     *
     * @since 0.9
     * @param array $item The Amazon API response converted from XML to an array.
     * @return null|Availability
     */
    public static function find_availability(array $item)
    {
        $availability = null;

        if(isset($item['Offers']['TotalOffers'])) {
            $total_offers = intval($item['Offers']['TotalOffers']);
            $availability = $total_offers > 0 ? Availability::available() : Availability::out_of_stock();
        }

        if($availability === null && isset($item['Offers']['Offer']['OfferListing']['AvailabilityAttributes']['AvailabilityType'])) {
            $type = $item['Offers']['Offer']['OfferListing']['AvailabilityAttributes']['AvailabilityType'];
            $availability = $type == 'now' ? Availability::available() : Availability::out_of_stock();
        }

        if($availability === null && isset($item['ItemAttributes']['ProductGroup']) && $item['ItemAttributes']['ProductGroup'] == 'eBooks') {
            $availability = Availability::available();
        }

        $availability = apply_filters('aff_amazon_helper_find_availability', $availability, $item);

        return $availability;
    }

    /**
     * Find the price in the Amazon API item response.
     *
     * @since 0.9
     * @param array $item The Amazon API response converted from XML to an array.
     * @return null|Money
     */
    public static function find_price(array $item)
    {
        /** @var Provider_Repository_Interface $provider_repository */
        $provider_repository = \Affilicious::get('affilicious.provider.repository.provider');

        $price = null;

        if(isset($item['Offers']['Offer']['OfferListing'])) {
            $offer_listing = $item['Offers']['Offer']['OfferListing'];
            $price = isset($offer_listing['SalePrice']) ? $offer_listing['SalePrice'] : $offer_listing['Price'];

            if(isset($price['Amount']) && isset($price['CurrencyCode'])) {
                $amount = floatval($price['Amount']) / 100;
                $currency = $price['CurrencyCode'];
                $price = new Money($amount, new Currency($currency));
            }
        }

        $price = apply_filters('aff_amazon_helper_find_price', $price, $item);

        return $price;
    }

    /**
     * Find the old price in the Amazon API item response.
     *
     * @since 0.9
     * @param array $item The Amazon API response converted from XML to an array.
     * @return null|Money
     */
    public static function find_old_price(array $item)
    {
        $old_price = null;

        if(isset($item['Offers']['Offer']['OfferListing'])) {
            $offerListing = $item['Offers']['Offer']['OfferListing'];
            $old_price = isset($offerListing['SalePrice']) && isset($offerListing['Price']) ? $offerListing['Price'] : null;

            if(isset($old_price['Amount']) && isset($old_price['CurrencyCode'])) {
                $amount = floatval($old_price['Amount']) / 100;
                $currency = $old_price['CurrencyCode'];
                $old_price = new Money($amount, new Currency($currency));
            }
        }

        $old_price = apply_filters('aff_amazon_helper_find_old_price', $old_price, $item);

        return $old_price;
    }

    /**
     * Find the product variant attributes in the Amazon API item response.
     *
     * @since 0.9
     * @param array $item The Amazon API response converted from XML to an array.
     * @param bool $create_missing
     * @return Attribute[]
     */
    public static function find_attributes(array $item, $create_missing = true)
    {
        /** @var Attribute_Template_Repository_Interface $attribute_template_repository */
        $attribute_template_repository = \Affilicious::get('affilicious.attribute.repository.attribute_template');

        /** @var Attribute_Template_Factory_Interface $attribute_template_factory */
        $attribute_template_factory = \Affilicious::get('affilicious.attribute.factory.attribute_template');

        $attributes = array();

        if(isset($item['VariationAttributes']['VariationAttribute'])) {
            $variation_attributes = $item['VariationAttributes']['VariationAttribute'];
            foreach ($variation_attributes as $variation_attribute) {
                // Find the attribute template
                $attribute_template = $attribute_template_repository->find_one_by_name(new Name($variation_attribute['Name']));
                if($attribute_template === null) {
                    $attribute_template = $attribute_template_factory->create_from_name(new Name($variation_attribute['Name']), Type::text());

                    if($create_missing) {
                        $attribute_template_repository->store($attribute_template);
                    }
                }

                // Build the attribute from the template.
                $attribute = $attribute_template->build(new Value($variation_attribute['Value']));
                $attributes[] = $attribute;
            }
        }

        $attributes = apply_filters('aff_amazon_helper_find_attributes', $attributes, $item);

        return $attributes;
    }

    /**
     * Find the shops in the Amazon API item response.
     *
     * @param array $item The Amazon API response converted from XML to an array.
     * @param Shop_Template_Id|null $shop_template_id
     * @param bool $create_missing
     * @return Shop|null
     */
    public static function find_shop(array $item, Shop_Template_Id $shop_template_id = null, $create_missing = true)
    {
        /** @var Shop_Template_Repository_Interface $shop_template_repository */
        $shop_template_repository = \Affilicious::get('affilicious.shop.repository.shop_template');

        /** @var Shop_Template_Factory_Interface $shop_template_factory */
        $shop_template_factory = \Affilicious::get('affilicious.shop.factory.shop_template');

        /** @var \Affilicious\Provider\Repository\Provider_Repository_Interface $provider_repository **/
        $provider_repository = \Affilicious::get('affilicious.provider.repository.provider');

        $tracking = self::find_tracking($item);
        if($tracking === null) {
            return null;
        }

        $pricing = self::find_pricing($item);
        if($pricing === null) {
            return null;
        }

        if($shop_template_id !== null) {
            $shop_template = $shop_template_repository->find_one_by_id($shop_template_id);
            if($shop_template === null) {
                return null;
            }
        } else {
            $shop_template = $shop_template_repository->find_one_by_name(new Name('Amazon'));
            if($shop_template === null) {
                $shop_template = $shop_template_factory->create_from_name(new Name('Amazon'));

                // Find the related Amazon provider
                $amazon_provider = $provider_repository->find_one_by_slug(new Slug('amazon'));
                if($amazon_provider !== null) {
                    $shop_template->set_provider_id($amazon_provider->get_id());
                }

                if($create_missing) {
                    $shop_template_repository->store($shop_template);
                }
            }
        }

        $shop = $shop_template->build($tracking, $pricing);
        $shop = apply_filters('aff_amazon_helper_find_shop', $shop, $item);

        return $shop;
    }

    /**
     * Create the product from the Amazon API response item.
     *
     * @since 0.9
     * @param array $item  The Amazon API response converted from XML to an array.
     * @param array $config
     * @param Complex_Product|null $parent
     * @return Product|\WP_Error
     */
    public static function create_product(array $item, array $config, Complex_Product $parent = null)
    {
        /** @var Slug_Generator_Interface $slug_generator */
        $slug_generator = \Affilicious::get('affilicious.common.generator.slug');

        $name = new Name($item['ItemAttributes']['Title']);
        $slug = $slug_generator->generate_from_name($name);

        if (!empty($config['variants']) && isset($item['Variations'])) {
            $product = new Complex_Product($name, $slug);
        } elseif(!empty($config['variants']) && $parent !== null && isset($item['VariationAttributes'])) {
            $product = new Product_Variant($parent, $name, $slug);
        } else {
            $product = new Simple_Product($name, $slug);
        }

        if ($product instanceof Complex_Product) {
            $variant_items = $item['Variations']['Item'];
            foreach ($variant_items as $variant_item) {

                // The variants doesn't have a affiliate link
                $variant_item = wp_parse_args($variant_item, [
                    'DetailPageURL' => $item['DetailPageURL']
                ]);

                /** @var Product_Variant $product_variant */
                $product_variant = self::create_product($variant_item, $config, $product);
                if($product_variant !== null) {
                    $product->add_variant($product_variant);
                }
            }

            $default_variant = $product->get_default_variant();
            if ($default_variant !== null) {
                $variant_thumbnail = $default_variant->get_thumbnail();
                $product->set_thumbnail($variant_thumbnail);
            }
        }

        if ($product instanceof Product_Variant) {
            $attributes = Amazon_Helper::find_attributes($item, !empty($config['store_attributes']));
            foreach ($attributes as $attribute) {
                $product->add_attribute($attribute);
            }
        }

        if ($product instanceof Shop_Aware_Interface) {
            $shop = Amazon_Helper::find_shop($item, null, !empty($config['store_shop']));
            if ($shop !== null) {
                $product->add_shop($shop);
            }
        }

        $thumbnail = Amazon_Helper::find_thumbnail($item, !empty($config['store_thumbnail']));
        if ($thumbnail !== null) {
            $product->set_thumbnail($thumbnail);
        }

        $image_gallery = Amazon_Helper::find_image_gallery($item, !empty($config['store_image_gallery']));
        if (!empty($image_gallery)) {
            $product->set_image_gallery($image_gallery);
        }

        return $product;
    }
}
