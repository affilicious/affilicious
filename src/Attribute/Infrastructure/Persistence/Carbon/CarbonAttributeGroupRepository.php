<?php
namespace Affilicious\Attribute\Infrastructure\Persistence\Carbon;

use Affilicious\Attribute\Domain\Model\Attribute\Attribute;
use Affilicious\Attribute\Domain\Model\Attribute\HelpText;
use Affilicious\Attribute\Domain\Model\Attribute\Key as AttributeKey;
use Affilicious\Attribute\Domain\Model\Attribute\Name;
use Affilicious\Attribute\Domain\Model\Attribute\Type;
use Affilicious\Attribute\Domain\Model\Attribute\Value;
use Affilicious\Attribute\Domain\Model\AttributeGroup;
use Affilicious\Attribute\Domain\Model\AttributeGroupId;
use Affilicious\Attribute\Domain\Model\AttributeGroupRepositoryInterface;
use Affilicious\Attribute\Domain\Model\Key;
use Affilicious\Attribute\Domain\Model\Title;
use Affilicious\Common\Application\Helper\DatabaseHelper;
use Affilicious\Common\Domain\Exception\InvalidPostTypeException;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class CarbonAttributeGroupRepository implements AttributeGroupRepositoryInterface
{
    const ATTRIBUTES = 'affilicious_attribute_group_attributes';
    const ATTRIBUTE_NAME = 'name';
    const ATTRIBUTE_TYPE = 'type';
    const ATTRIBUTE_VALUE = 'value';
    const ATTRIBUTE_HELP_TEXT = 'help_text';

    /**
     * @inheritdoc
     */
    public function findById(AttributeGroupId $attributeGroupId)
    {
        $post = get_post($attributeGroupId->getValue());
        if ($post === null) {
            return null;
        }

        $attributeGroup = $this->buildAttributeGroupFromPost($post);
        return $attributeGroup;
    }

    /**
     * @inheritdoc
     */
    public function findAll()
    {
        $query = new \WP_Query(array(
            'post_type' => AttributeGroup::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        $attributeGroups = array();
        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $attributeGroup = self::buildAttributeGroupFromPost($query->post);
                $attributeGroups[] = $attributeGroup;
            }

            wp_reset_postdata();
        }

        return $attributeGroups;
    }

    /**
     * Convert the post into a attribute group
     *
     * @since 0.3
     * @param \WP_Post $post
     * @return AttributeGroup
     */
    private function buildAttributeGroupFromPost(\WP_Post $post)
    {
        if($post->post_type !== AttributeGroup::POST_TYPE) {
            throw new InvalidPostTypeException($post->post_type, AttributeGroup::POST_TYPE);
        }

        $attributeGroup = new AttributeGroup(
            new AttributeGroupId($post->ID),
            new Key(DatabaseHelper::convertTextToKey($post->post_title)),
            new Title($post->post_title)
        );

        $fields = carbon_get_post_meta($attributeGroup->getId()->getValue(), self::ATTRIBUTES, 'complex');
        if (!empty($fields)) {
            foreach ($fields as $field) {
                $name = $field[self::ATTRIBUTE_NAME];
                $key = DatabaseHelper::convertTextToKey($name);
                $type = $field[self::ATTRIBUTE_TYPE];
                $value = $field[self::ATTRIBUTE_VALUE];
                $helpText = $field[self::ATTRIBUTE_HELP_TEXT];

                $attribute = new Attribute(
                    new AttributeKey($key),
                    new Name($name),
                    new Type($type),
                    new Value($value)
                );

                if(!empty($helpText)) {
                    $attribute->setHelpText(new HelpText($helpText));
                }

                $attributeGroup->addAttribute($attribute);
            }
        }

        return $attributeGroup;
    }
}
