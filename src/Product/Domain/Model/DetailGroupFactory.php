<?php
namespace Affilicious\ProductsPlugin\Product\Domain\Model;

if(!defined('ABSPATH')) exit('Not allowed to access pages directly.');

class DetailGroupFactory
{
    /**
     * Create a new detail group from an existing product and field group
     * @param Product $product
     * @param FieldGroup $fieldGroup
     * @return DetailGroup
     */
    public function create(Product $product, FieldGroup $fieldGroup)
    {
        $detailGroup = new DetailGroup($fieldGroup->getId());
        $fields = $fieldGroup->getFields();

        foreach ($fields as $field) {
            $value = carbon_get_post_meta($product->getId(), sprintf(
                Detail::ID_TEMPLATE,
                $fieldGroup->getId(),
                $field->getKey()
            ));

            $detail = new Detail(
                $field->getKey(),
                $field->getType(),
                $field->getLabel(),
                !empty($value) ? $value : null
            );

            $detailGroup->addDetail($detail);
        }

        return $detailGroup;
    }
}
