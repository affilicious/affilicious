<?php
namespace Affilicious\Attribute\Migration;

use Affilicious\Common\Model\Name;
use Affilicious\Attribute\Factory\Attribute_Template_Factory_Interface;
use Affilicious\Attribute\Model\Type;
use Affilicious\Attribute\Model\Unit;
use Affilicious\Attribute\Repository\Attribute_Template_Repository_Interface;
use Carbon_Fields\Container as Carbon_Container;
use Carbon_Fields\Field as Carbon_Field;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Post_To_Term_Migration
{
    /**
     * @var Attribute_Template_Factory_Interface
     */
    private $attribute_template_factory;

    /**
     * @var Attribute_Template_Repository_Interface
     */
    private $attribute_template_repository;

    /**
     * @since 0.8
     * @param Attribute_Template_Factory_Interface $attribute_template_factory
     * @param Attribute_Template_Repository_Interface $attribute_template_repository
     */
    public function __construct(
        Attribute_Template_Factory_Interface $attribute_template_factory,
        Attribute_Template_Repository_Interface $attribute_template_repository
    ) {
        $this->attribute_template_factory = $attribute_template_factory;
        $this->attribute_template_repository = $attribute_template_repository;
    }

    /**
     * Migrate the attribute templates posts to taxonomy terms.
     *
     * @since 0.8
     */
    public function migrate()
    {
        $posts = get_posts(array(
            'post_type' => 'aff_attr_template',
            'status' => 'published'
        ));

        foreach ($posts as $post) {
            $fields = carbon_get_post_meta($post->ID, '_affilicious_attribute_group_attributes', 'complex');
            if(!empty($fields)) {
                foreach ($fields as $field) {
                    $name = isset($field['title']) ? $field['title'] : null;
                    $type = isset($field['type']) ? $field['type'] : null;
                    $unit = isset($field['unit']) ? $field['unit'] : null;

                    if(empty($name) || empty($type)) {
                        continue;
                    }

                    $attribute_template = $this->attribute_template_factory->create_from_name(
                        new Name($name),
                        new Type($type),
                        !empty($unit) ? new Unit($unit) : null
                    );

                    try {
                        $this->attribute_template_repository->store($attribute_template);
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }

            wp_delete_post($post->ID);
        }
    }
}
