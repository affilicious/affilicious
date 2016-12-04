<?php
namespace Affilicious\Attribute\Infrastructure\Repository\Carbon;

use Affilicious\Attribute\Domain\Model\Attribute\Attribute_Template;
use Affilicious\Attribute\Domain\Model\Attribute\Attribute_Template_Factory_Interface;
use Affilicious\Attribute\Domain\Model\Attribute\Help_Text;
use Affilicious\Attribute\Domain\Model\Attribute\Type;
use Affilicious\Attribute\Domain\Model\Attribute\Unit;
use Affilicious\Attribute\Domain\Model\Attribute_Template_Group;
use Affilicious\Attribute\Domain\Model\Attribute_Template_Group_Factory_Interface;
use Affilicious\Attribute\Domain\Model\Attribute_Template_Group_Id;
use Affilicious\Attribute\Domain\Model\Attribute_Template_Group_Repository_Interface;
use Affilicious\Common\Domain\Exception\Invalid_Post_Type_Exception;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Common\Infrastructure\Repository\Carbon\Abstract_Carbon_Repository;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Carbon_Attribute_Template_Group_Repository extends Abstract_Carbon_Repository implements Attribute_Template_Group_Repository_Interface
{
    const ATTRIBUTES = 'affilicious_attribute_group_attributes';
    const ATTRIBUTE_TITLE = 'title';
    const ATTRIBUTE_TYPE = 'type';
    const ATTRIBUTE_UNIT = 'unit';
    const ATTRIBUTE_HELP_TEXT = 'help_text';

    /**
     * @var Attribute_Template_Group_Factory_Interface
     */
    protected $attribute_template_group_factory;

    /**
     * @var Attribute_Template_Factory_Interface
     */
    protected $attribute_template_factory;

    /**
     * @since 0.6
     * @param Attribute_Template_Group_Factory_Interface $attribute_template_group_factory
     * @param Attribute_Template_Factory_Interface $attribute_template_factory
     */
    public function __construct(
        Attribute_Template_Group_Factory_Interface $attribute_template_group_factory,
        Attribute_Template_Factory_Interface $attribute_template_factory
    )
    {
        $this->attribute_template_group_factory = $attribute_template_group_factory;
        $this->attribute_template_factory = $attribute_template_factory;
    }

    /**
     * @inheritdoc
     */
    public function find_by_id(Attribute_Template_Group_Id $attribute_group_id)
    {
        $post = get_post($attribute_group_id->get_value());
        if ($post === null || $post->post_status !== 'publish') {
            return null;
        }

        if($post->post_type !== Attribute_Template_Group::POST_TYPE) {
            throw new Invalid_Post_Type_Exception($post->post_type, Attribute_Template_Group::POST_TYPE);
        }

        $attribute_group = $this->build_attribute_template_group_from_post($post);
        return $attribute_group;
    }

    /**
     * @inheritdoc
     */
    public function find_all()
    {
        $query = new \WP_Query(array(
            'post_type' => Attribute_Template_Group::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        $attribute_groups = array();
        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $attribute_group = self::build_attribute_template_group_from_post($query->post);
                $attribute_groups[] = $attribute_group;
            }

            wp_reset_postdata();
        }

        return $attribute_groups;
    }

    /**
     * Convert the post into a attribute template group.
     *
     * @since 0.6
     * @param \WP_Post $post
     * @return Attribute_Template_Group
     */
    protected function build_attribute_template_group_from_post(\WP_Post $post)
    {
        if($post->post_type !== Attribute_Template_Group::POST_TYPE) {
            throw new Invalid_Post_Type_Exception($post->post_type, Attribute_Template_Group::POST_TYPE);
        }

        $attribute_group = $this->attribute_template_group_factory->create(
            new Title($post->post_title),
            new Name($post->post_name)
        );

        $attribute_group = $this->add_id($attribute_group, $post);
        $attribute_group = $this->add_attributes($attribute_group);
        $attribute_group = $this->add_updated_at($attribute_group, $post);

        return $attribute_group;
    }

    /**
     * Add the ID to the attribute template group.
     *
     * @since 0.7
     * @param Attribute_Template_Group $attribute_template_group
     * @param \WP_Post $post
     * @return Attribute_Template_Group
     */
    protected function add_id(Attribute_Template_Group $attribute_template_group, \WP_Post $post)
    {
        $attribute_template_group->set_id(new Attribute_Template_Group_Id($post->ID));

        return $attribute_template_group;
    }

    /**
     * Add the attribute templates to the attribute template group.
     *
     * @since 0.6
     * @param Attribute_Template_Group $attribute_group
     * @return Attribute_Template_Group
     */
    protected function add_attributes(Attribute_Template_Group $attribute_group)
    {
        $raw_attribute_templates = carbon_get_post_meta($attribute_group->get_id()->get_value(), self::ATTRIBUTES, 'complex');
        if (!empty($raw_attribute_templates)) {
            foreach ($raw_attribute_templates as $raw_attribute_template) {
                $attribute = $this->get_attribute_template_from_array($raw_attribute_template);

                if(!empty($attribute)) {
                    $attribute_group->add_attribute_template($attribute);
                }
            }
        }

        return $attribute_group;
    }

    /**
     * Add the date and time of the last update to the shop template.
     *
     * @since 0.7
     * @param Attribute_Template_Group $attribute_template_group
     * @param \WP_Post $post
     * @return Attribute_Template_Group
     */
    protected function add_updated_at(Attribute_Template_Group $attribute_template_group, \WP_Post $post)
    {
        $updated_at = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $post->post_modified);
        $attribute_template_group->set_updated_at($updated_at);

        return $attribute_template_group;
    }

    /**
     * Build the attribute template from the array.
     *
     * @since 0.6
     * @param array $raw_attribute_template
     * @return null|Attribute_Template
     */
    protected function get_attribute_template_from_array(array $raw_attribute_template)
    {
        $title = isset($raw_attribute_template[self::ATTRIBUTE_TITLE]) ? $raw_attribute_template[self::ATTRIBUTE_TITLE] : null;
        $type = isset($raw_attribute_template[self::ATTRIBUTE_TYPE]) ? $raw_attribute_template[self::ATTRIBUTE_TYPE] : null;
        $unit = isset($raw_attribute_template[self::ATTRIBUTE_UNIT]) ? $raw_attribute_template[self::ATTRIBUTE_UNIT] : null;
        $help_text = isset($raw_attribute_template[self::ATTRIBUTE_HELP_TEXT]) ? $raw_attribute_template[self::ATTRIBUTE_HELP_TEXT] : null;

        if(empty($title) || empty($type)) {
            return null;
        }

        $attribute_template = $this->attribute_template_factory->create(
            new Title($title),
            new Type($type)
        );

        if(!empty($unit)) {
            $attribute_template->set_unit(new Unit($unit));
        }

        if(!empty($help_text)) {
            $attribute_template->set_help_text(new Help_Text($help_text));
        }

        return $attribute_template;
    }
}
