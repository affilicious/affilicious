<?php
namespace Affilicious\Attribute\Infrastructure\Persistence\Carbon;

use Affilicious\Attribute\Domain\Model\AttributeTemplate\AttributeTemplate;
use Affilicious\Attribute\Domain\Model\AttributeTemplate\AttributeTemplateFactoryInterface;
use Affilicious\Attribute\Domain\Model\AttributeTemplate\HelpText;
use Affilicious\Attribute\Domain\Model\AttributeTemplate\Type;
use Affilicious\Attribute\Domain\Model\AttributeTemplate\Unit;
use Affilicious\Attribute\Domain\Model\AttributeTemplate\Value;
use Affilicious\Attribute\Domain\Model\AttributeTemplateGroup;
use Affilicious\Attribute\Domain\Model\AttributeTemplateGroupFactoryInterface;
use Affilicious\Attribute\Domain\Model\AttributeTemplateGroupId;
use Affilicious\Attribute\Domain\Model\AttributeTemplateGroupRepositoryInterface;
use Affilicious\Common\Domain\Exception\InvalidPostTypeException;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Common\Infrastructure\Persistence\Carbon\AbstractCarbonRepository;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class CarbonAttributeTemplateGroupRepository extends AbstractCarbonRepository implements AttributeTemplateGroupRepositoryInterface
{
    const ATTRIBUTES = 'affilicious_attribute_group_attributes';
    const ATTRIBUTE_TITLE = 'title';
    const ATTRIBUTE_TYPE = 'type';
    const ATTRIBUTE_VALUE = 'value';
    const ATTRIBUTE_UNIT = 'unit';
    const ATTRIBUTE_HELP_TEXT = 'help_text';

    /**
     * @var AttributeTemplateGroupFactoryInterface
     */
    protected $attributeTemplateGroupFactory;

    /**
     * @var AttributeTemplateFactoryInterface
     */
    protected $attributeTemplateFactory;

    /**
     * @since 0.6
     * @param AttributeTemplateGroupFactoryInterface $attributeTemplateGroupFactory
     * @param AttributeTemplateFactoryInterface $attributeTemplateFactory
     */
    public function __construct(
        AttributeTemplateGroupFactoryInterface $attributeTemplateGroupFactory,
        AttributeTemplateFactoryInterface $attributeTemplateFactory
    )
    {
        $this->attributeTemplateGroupFactory = $attributeTemplateGroupFactory;
        $this->attributeTemplateFactory = $attributeTemplateFactory;
    }

    /**
     * @inheritdoc
     */
    public function findById(AttributeTemplateGroupId $attributeGroupId)
    {
        $post = get_post($attributeGroupId->getValue());
        if ($post === null || $post->post_status !== 'publish') {
            return null;
        }

        if($post->post_type !== AttributeTemplateGroup::POST_TYPE) {
            throw new InvalidPostTypeException($post->post_type, AttributeTemplateGroup::POST_TYPE);
        }

        $attributeGroup = $this->buildAttributeTemplateGroupFromPost($post);
        return $attributeGroup;
    }

    /**
     * @inheritdoc
     */
    public function findAll()
    {
        $query = new \WP_Query(array(
            'post_type' => AttributeTemplateGroup::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        $attributeGroups = array();
        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $attributeGroup = self::buildAttributeTemplateGroupFromPost($query->post);
                $attributeGroups[] = $attributeGroup;
            }

            wp_reset_postdata();
        }

        return $attributeGroups;
    }

    /**
     * Convert the post into a attribute template group
     *
     * @since 0.6
     * @param \WP_Post $post
     * @return AttributeTemplateGroup
     */
    protected function buildAttributeTemplateGroupFromPost(\WP_Post $post)
    {
        if($post->post_type !== AttributeTemplateGroup::POST_TYPE) {
            throw new InvalidPostTypeException($post->post_type, AttributeTemplateGroup::POST_TYPE);
        }

        // Title, Name, Key
        $attributeGroup = $this->attributeTemplateGroupFactory->create(
            new Title($post->post_title),
            new Name($post->post_name)
        );

        // ID
        $attributeGroup->setId(new AttributeTemplateGroupId($post->ID));

        // Attributes
        $attributeGroup = $this->addAttributes($attributeGroup);

        return $attributeGroup;
    }

    /**
     * Add the attribute templates to the attribute template group
     *
     * @since 0.6
     * @param AttributeTemplateGroup $attributeGroup
     * @return AttributeTemplateGroup
     */
    protected function addAttributes(AttributeTemplateGroup $attributeGroup)
    {
        $rawAttributeTemplates = carbon_get_post_meta($attributeGroup->getId()->getValue(), self::ATTRIBUTES, 'complex');
        if (!empty($rawAttributeTemplates)) {
            foreach ($rawAttributeTemplates as $rawAttributeTemplate) {
                $attribute = $this->getAttributeTemplateFromArray($rawAttributeTemplate);

                if(!empty($attribute)) {
                    $attributeGroup->addAttributeTemplate($attribute);
                }
            }
        }

        return $attributeGroup;
    }

    /**
     * Build the attribute template from the array
     *
     * @since 0.6
     * @param array $rawAttributeTemplate
     * @return null|AttributeTemplate
     */
    protected function getAttributeTemplateFromArray(array $rawAttributeTemplate)
    {
        $title = isset($rawAttributeTemplate[self::ATTRIBUTE_TITLE]) ? $rawAttributeTemplate[self::ATTRIBUTE_TITLE] : null;
        $type = isset($rawAttributeTemplate[self::ATTRIBUTE_TYPE]) ? $rawAttributeTemplate[self::ATTRIBUTE_TYPE] : null;
        $value = isset($rawAttributeTemplate[self::ATTRIBUTE_VALUE]) ? $rawAttributeTemplate[self::ATTRIBUTE_VALUE] : null;
        $unit = isset($rawAttributeTemplate[self::ATTRIBUTE_UNIT]) ? $rawAttributeTemplate[self::ATTRIBUTE_UNIT] : null;
        $helpText = isset($rawAttributeTemplate[self::ATTRIBUTE_HELP_TEXT]) ? $rawAttributeTemplate[self::ATTRIBUTE_HELP_TEXT] : null;

        if(empty($title) || empty($type) || empty($value)) {
            return null;
        }

        $attributeTemplate = $this->attributeTemplateFactory->create(
            new Title($title),
            new Type($type),
            new Value($value)
        );

        if(!empty($unit)) {
            $attributeTemplate->setUnit(new Unit($unit));
        }

        if(!empty($helpText)) {
            $attributeTemplate->setHelpText(new HelpText($helpText));
        }

        return $attributeTemplate;
    }
}
