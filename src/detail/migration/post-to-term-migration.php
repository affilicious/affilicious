<?php
namespace Affilicious\Detail\Migration;

use Affilicious\Common\Model\Name;
use Affilicious\Detail\Factory\Detail_Template_Factory_Interface;
use Affilicious\Detail\Model\Type;
use Affilicious\Detail\Model\Unit;
use Affilicious\Detail\Repository\Detail_Template_Repository_Interface;

if (!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

/**
 * @since 0.8
 */
class Post_To_Term_Migration
{
    /**
     * @since 0.8
     * @var Detail_Template_Factory_Interface
     */
    protected $detail_template_factory;

    /**
     * @since 0.8
     * @var Detail_Template_Repository_Interface
     */
    protected $detail_template_repository;

    /**
     * @since 0.8
     * @param Detail_Template_Factory_Interface $detail_template_factory
     * @param Detail_Template_Repository_Interface $detail_template_repository
     */
    public function __construct(
        Detail_Template_Factory_Interface $detail_template_factory,
        Detail_Template_Repository_Interface $detail_template_repository
    ) {
        $this->detail_template_factory = $detail_template_factory;
        $this->detail_template_repository = $detail_template_repository;
    }

    /**
     * Migrate the detail templates posts to taxonomy terms.
     *
     * @since 0.8
     */
    public function migrate()
    {
        $posts = get_posts(array(
            'post_type' => 'detail_group',
            'status' => 'publish'
        ));
        
        foreach ($posts as $post) {
            $fields = carbon_get_post_meta($post->ID, '_affilicious_detail_group_fields', 'complex');
            if(!empty($fields)) {
                foreach ($fields as $field) {
                    $name = isset($field['name']) ? $field['name'] : null;
                    $type = isset($field['type']) ? $field['type'] : null;
                    $unit = isset($field['unit']) ? $field['unit'] : null;

                    if(empty($name) || empty($type)) {
                        continue;
                    }

                    $detail_template = $this->detail_template_factory->create_from_name(
                        new Name($name),
                        new Type($type),
                        !empty($unit) ? new Unit($unit) : null
                    );

                    try {
                        $this->detail_template_repository->store($detail_template);
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }

            wp_delete_post($post->ID);
        }
    }
}
