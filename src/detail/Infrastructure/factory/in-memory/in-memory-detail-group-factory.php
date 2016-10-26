<?php
namespace Affilicious\Detail\Infrastructure\Factory\In_Memory;

use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Detail\Domain\Model\Detail\Detail;
use Affilicious\Detail\Domain\Model\Detail\Value;
use Affilicious\Detail\Domain\Model\Detail_Group;
use Affilicious\Detail\Domain\Model\Detail_Group_Factory_Interface;
use Affilicious\Detail\Domain\Model\Detail_Template_Group_id;
use Affilicious\Detail\Domain\Model\Detail_Template_Group_Repository_Interface;
use Affilicious\Detail\Domain\Model\Detail\Type;
use Affilicious\Detail\Domain\Model\Detail\Unit;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class In_Memory_Detail_Group_Factory implements Detail_Group_Factory_Interface
{
    /**
     * @var Detail_Template_Group_Repository_Interface
     */
    private $detail_template_group_repository;

    /**
     * @since 0.6
     * @param Detail_Template_Group_Repository_Interface $detail_template_group_repository
     */
    public function __construct(Detail_Template_Group_Repository_Interface $detail_template_group_repository)
    {
        $this->detail_template_group_repository = $detail_template_group_repository;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Title $title, Name $name, Key $key)
    {
        $detail_group = new Detail_Group($title, $name, $key);

        return $detail_group;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create_from_template_id_and_data(Detail_Template_Group_id $detail_template_group_id, $data)
    {
        $detail_template_group = $this->detail_template_group_repository->find_by_id($detail_template_group_id);
        if($detail_template_group === null || !is_array($data)) {
            return null;
        }

        $detail_group = $this->create(
            $detail_template_group->get_title(),
            $detail_template_group->get_name(),
            $detail_template_group->get_key()
        );

        $detail_group->set_template_id($detail_template_group_id);

        $detail_templates = $detail_template_group->get_detail_templates();
        foreach ($detail_templates as $detail_template) {
            $detail = new Detail(
                $detail_template->get_title(),
                $detail_template->get_name(),
                $detail_template->get_key(),
                new Type($detail_template->get_type()->get_value())
            );

            if($detail_template->has_unit()) {
                $detail->set_unit(new Unit($detail_template->get_unit()->get_value()));
            }

            if(!empty($data[$detail->get_key()->get_value()])) {
                $value = $data[$detail->get_key()->get_value()];

                // Convert the string into a float, if the type is numeric
                $value = $detail->get_type()->is_equal_to(Type::number()) ? floatval($value) : $value;

                $detail->set_value(new Value($value));
            }

            $detail_group->add_detail($detail);
        }

        return $detail_group;
    }
}
