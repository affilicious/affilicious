<?php
namespace Affilicious\Detail\Infrastructure\Repository\Carbon;

use Affilicious\Common\Domain\Exception\Invalid_Post_Type_Exception;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Common\Infrastructure\Repository\Carbon\Abstract_Carbon_Repository;
use Affilicious\Detail\Domain\Model\Detail\Detail_Template;
use Affilicious\Detail\Domain\Model\Detail\Detail_Template_Factory_Interface;
use Affilicious\Detail\Domain\Model\Detail\Help_Text;
use Affilicious\Detail\Domain\Model\Detail\Type;
use Affilicious\Detail\Domain\Model\Detail\Unit;
use Affilicious\Detail\Domain\Model\Detail_Template_Group;
use Affilicious\Detail\Domain\Model\Detail_Template_Group_Factory_Interface;
use Affilicious\Detail\Domain\Model\Detail_Template_Group_Id;
use Affilicious\Detail\Domain\Model\Detail_Template_Group_Repository_Interface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Carbon_Detail_Template_Group_Repository extends Abstract_Carbon_Repository implements Detail_Template_Group_Repository_Interface
{
    //TODO: Convert the database structure from name to title
    const DETAILS = 'affilicious_detail_group_fields';
    const DETAIL_TITLE = 'name';
    const DETAIL_TYPE = 'type';
    const DETAIL_UNIT = 'unit';
    const DETAIL_HELP_TEXT = 'help_text';

    /**
     * @var Detail_Template_Group_Factory_Interface
     */
    protected $detail_template_group_factory;

    /**
     * @var Detail_Template_Factory_Interface
     */
    protected $detail_template_factory;

    /**
     * @since 0.6
     * @param Detail_Template_Group_Factory_Interface $detail_template_group_factory
     * @param Detail_Template_Factory_Interface $detail_template_factory
     */
    public function __construct(
        Detail_Template_Group_Factory_Interface $detail_template_group_factory,
        Detail_Template_Factory_Interface $detail_template_factory
    )
    {
        $this->detail_template_group_factory = $detail_template_group_factory;
        $this->detail_template_factory = $detail_template_factory;
    }

    /**
     * @inheritdoc
     */
    public function find_by_id(Detail_Template_Group_Id $detail_template_group_id)
    {
        $post = get_post($detail_template_group_id->get_value());
        if ($post === null || $post->post_status !== 'publish') {
            return null;
        }

        $detail_template_group = $this->build_detail_group_from_post($post);
        return $detail_template_group;
    }

    /**
     * @inheritdoc
     */
    public function find_all()
    {
        $query = new \WP_Query(array(
            'post_type' => Detail_Template_Group::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        $detail_template_groups = array();
        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $detail_template_group = self::build_detail_group_from_post($query->post);
                $detail_template_groups[] = $detail_template_group;
            }

            wp_reset_postdata();
        }

        return $detail_template_groups;
    }

    /**
     * Convert the post into a detail template group.
     *
     * @since 0.6
     * @param \WP_Post $post
     * @return Detail_Template_Group
     */
    protected function build_detail_group_from_post(\WP_Post $post)
    {
        if($post->post_type !== Detail_Template_Group::POST_TYPE) {
            throw new Invalid_Post_Type_Exception($post->post_type, Detail_Template_Group::POST_TYPE);
        }

        $detail_template_group = $this->detail_template_group_factory->create(
            new Title($post->post_title),
            new Name($post->post_name)
        );

        $detail_template_group = $this->add_id($detail_template_group, $post);
        $detail_template_group = $this->add_details($detail_template_group);
        $detail_template_group = $this->add_updated_at($detail_template_group, $post);

        return $detail_template_group;
    }

    /**
     * Add the ID to the detail template group.
     *
     * @since 0.7
     * @param Detail_Template_Group $detail_template_group
     * @param \WP_Post $post
     * @return Detail_Template_Group
     */
    protected function add_id(Detail_Template_Group $detail_template_group, \WP_Post $post)
    {
        $detail_template_group->set_id(new Detail_Template_Group_Id($post->ID));

        return $detail_template_group;
    }

    /**
     * Add the detail templates to the detail template group.
     *
     * @since 0.6
     * @param Detail_Template_Group $detail_group
     * @return Detail_Template_Group
     */
    protected function add_details(Detail_Template_Group $detail_group)
    {
        $raw_detail_templates = carbon_get_post_meta($detail_group->get_id()->get_value(), self::DETAILS, 'complex');
        if (!empty($raw_detail_templates)) {
            foreach ($raw_detail_templates as $raw_detail_template) {
                $detail = $this->build_detail_template_from_array($raw_detail_template);

                if(!empty($detail)) {
                    $detail_group->add_detail_template($detail);
                }
            }
        }

        return $detail_group;
    }

    /**
     * Add the date and time of the last update to the shop template.
     *
     * @since 0.7
     * @param Detail_Template_Group $detail_template_group
     * @param \WP_Post $post
     * @return Detail_Template_Group
     */
    protected function add_updated_at(Detail_Template_Group $detail_template_group, \WP_Post $post)
    {
        $updated_at = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $post->post_modified);
        $detail_template_group->set_updated_at($updated_at);

        return $detail_template_group;
    }

    /**
     * Build the detail template from the array
     *
     * @since 0.6
     * @param array $raw_detail_template
     * @return null|Detail_Template
     */
    protected function build_detail_template_from_array(array $raw_detail_template)
    {
        $title = isset($raw_detail_template[self::DETAIL_TITLE]) ? $raw_detail_template[self::DETAIL_TITLE] : null;
        $type = isset($raw_detail_template[self::DETAIL_TYPE]) ? $raw_detail_template[self::DETAIL_TYPE] : null;
        $unit = isset($raw_detail_template[self::DETAIL_UNIT]) ? $raw_detail_template[self::DETAIL_UNIT] : null;
        $help_text = isset($raw_detail_template[self::DETAIL_HELP_TEXT]) ? $raw_detail_template[self::DETAIL_HELP_TEXT] : null;

        if(empty($title) || empty($type)) {
            return null;
        }

        $detail_template = $this->detail_template_factory->create(
            new Title($title),
            new Type($type)
        );

        if(!empty($unit)) {
            $detail_template->set_unit(new Unit($unit));
        }

        if(!empty($help_text)) {
            $detail_template->set_help_text(new Help_Text($help_text));
        }

        return $detail_template;
    }
}
