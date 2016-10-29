<?php
namespace Affilicious\Attribute\Infrastructure\Factory\In_Memory;

use Affilicious\Attribute\Domain\Model\Attribute\Attribute;
use Affilicious\Attribute\Domain\Model\Attribute\Type;
use Affilicious\Attribute\Domain\Model\Attribute\Unit;
use Affilicious\Attribute\Domain\Model\Attribute\Value;
use Affilicious\Attribute\Domain\Model\Attribute_Group;
use Affilicious\Attribute\Domain\Model\Attribute_Group_Factory_Interface;
use Affilicious\Attribute\Domain\Model\Attribute_Template_Group_Id;
use Affilicious\Attribute\Domain\Model\Attribute_Template_Group_Repository_Interface;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Infrastructure\Repository\Carbon\Carbon_Product_Repository;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Attribute_Group_Factory implements Attribute_Group_Factory_Interface
{
    /**
     * @var Attribute_Template_Group_Repository_Interface
     */
    private $attribute_template_group_repository;

    /**
     * @since 0.6
     * @param Attribute_Template_Group_Repository_Interface $attribute_template_group_repository
     */
    public function __construct(Attribute_Template_Group_Repository_Interface $attribute_template_group_repository)
    {
        $this->attribute_template_group_repository = $attribute_template_group_repository;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Title $title, Name $name, Key $key)
    {
        $attribute_group = new Attribute_Group($title, $name, $key);

        return $attribute_group;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create_from_template_id_and_data(Attribute_Template_Group_Id $attribute_template_group_id, $data)
    {
        if(empty($data)) {
            return null;
        }

        $attribute_template_group = $this->attribute_template_group_repository->find_by_id($attribute_template_group_id);
        if($attribute_template_group === null || !is_array($data)) {
            return null;
        }

        $attribute_group = $this->create(
            $attribute_template_group->get_title(),
            $attribute_template_group->get_name(),
            $attribute_template_group->get_key()
        );

        $attribute_group->set_template_id($attribute_template_group_id);

        $attribute_templates = $attribute_template_group->get_attribute_templates();
        foreach ($attribute_templates as $index => $attribute_template) {
            if(!isset($data[$index])) {
                return null;
            }

            $raw_attribute = $data[$index];
            if(empty($raw_attribute)) {
                return null;
            }

            $custom_value = $raw_attribute[Carbon_Product_Repository::VARIANT_ATTRIBUTES_CUSTOM_VALUE];
            $custom_value = $attribute_template->get_type()->is_equal_to(Type::number()) ? floatval($custom_value) : $custom_value;

            $attribute = new Attribute(
                $attribute_template->get_title(),
                $attribute_template->get_name(),
                $attribute_template->get_key(),
                new Type($attribute_template->get_type()->get_value()),
                new Value($custom_value)
            );

            if($attribute_template->has_unit()) {
                $attribute->set_unit(new Unit($attribute_template->get_unit()->get_value()));
            }

            $attribute_group->add_attribute($attribute);
        }

        return $attribute_group;
    }
}
