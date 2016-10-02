<?php
namespace Affilicious\Attribute\Infrastructure\Persistence\Carbon;

use Affilicious\Attribute\Domain\Model\Attribute\Attribute;
use Affilicious\Attribute\Domain\Model\Attribute\AttributeFactoryInterface;
use Affilicious\Attribute\Domain\Model\Attribute\HelpText;
use Affilicious\Attribute\Domain\Model\Attribute\Type;
use Affilicious\Attribute\Domain\Model\Attribute\Value;
use Affilicious\Attribute\Domain\Model\AttributeGroup;
use Affilicious\Attribute\Domain\Model\AttributeGroupFactoryInterface;
use Affilicious\Attribute\Domain\Model\AttributeGroupId;
use Affilicious\Attribute\Domain\Model\AttributeGroupRepositoryInterface;
use Affilicious\Common\Domain\Exception\InvalidPostTypeException;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class CarbonAttributeGroupRepository implements AttributeGroupRepositoryInterface
{
    const ATTRIBUTES = 'affilicious_attribute_group_attributes';
    const ATTRIBUTE_TITLE = 'title';
    const ATTRIBUTE_TYPE = 'type';
    const ATTRIBUTE_VALUE = 'value';
    const ATTRIBUTE_HELP_TEXT = 'help_text';

    /**
     * @var AttributeGroupFactoryInterface
     */
    protected $attributeGroupFactory;

    /**
     * @var AttributeFactoryInterface
     */
    protected $attributeFactory;

    /**
     * @since 0.6
     * @param AttributeGroupFactoryInterface $attributeGroupFactory
     * @param AttributeFactoryInterface $attributeFactory
     */
    public function __construct(
        AttributeGroupFactoryInterface $attributeGroupFactory,
        AttributeFactoryInterface $attributeFactory
    )
    {
        $this->attributeGroupFactory = $attributeGroupFactory;
        $this->attributeFactory = $attributeFactory;
    }

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
    protected function buildAttributeGroupFromPost(\WP_Post $post)
    {
        if($post->post_type !== AttributeGroup::POST_TYPE) {
            throw new InvalidPostTypeException($post->post_type, AttributeGroup::POST_TYPE);
        }

        // Title, Name, Key
        $attributeGroup = $this->attributeGroupFactory->create(
            new Title($post->post_title),
            new Name($post->post_name)
        );

        // ID
        $attributeGroup->setId(new AttributeGroupId($post->ID));

        // Attributes
        $attributeGroup = $this->addAttributes($attributeGroup);

        return $attributeGroup;
    }

    /**
     * Add the attributes to the attribute group
     *
     * @since 0.6
     * @param AttributeGroup $attributeGroup
     * @return AttributeGroup
     */
    protected function addAttributes(AttributeGroup $attributeGroup)
    {
        $rawAttributes = carbon_get_post_meta($attributeGroup->getId()->getValue(), self::ATTRIBUTES, 'complex');
        if (!empty($rawAttributes)) {
            foreach ($rawAttributes as $rawAttribute) {
                $attribute = $this->buildAttributeFromArray($rawAttribute);

                if(!empty($attribute)) {
                    $attributeGroup->addAttribute($attribute);
                }
            }
        }

        return $attributeGroup;
    }

    /**
     * Build the attribute from the array
     *
     * @since 0.6
     * @param array $rawAttribute
     * @return null|Attribute
     */
    protected function buildAttributeFromArray(array $rawAttribute)
    {
        $title = isset($rawAttribute[self::ATTRIBUTE_TITLE]) ? $rawAttribute[self::ATTRIBUTE_TITLE] : null;
        $type = isset($rawAttribute[self::ATTRIBUTE_TYPE]) ? $rawAttribute[self::ATTRIBUTE_TYPE] : null;
        $value = isset($rawAttribute[self::ATTRIBUTE_VALUE]) ? $rawAttribute[self::ATTRIBUTE_VALUE] : null;
        $helpText = isset($rawAttribute[self::ATTRIBUTE_HELP_TEXT]) ? $rawAttribute[self::ATTRIBUTE_HELP_TEXT] : null;

        if(empty($title) || empty($type) || empty($value)) {
            return null;
        }

        $attribute = $this->attributeFactory->create(
            new Title($title),
            new Type($type),
            new Value($value)
        );

        if(!empty($helpText)) {
            $attribute->setHelpText(new HelpText($helpText));
        }

        return $attribute;
    }
}
