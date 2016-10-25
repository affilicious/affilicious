<?php
namespace Affilicious\Attribute\Infrastructure\Factory\InMemory;

use Affilicious\Attribute\Domain\Model\Attribute\Attribute;
use Affilicious\Attribute\Domain\Model\Attribute\Type;
use Affilicious\Attribute\Domain\Model\Attribute\Unit;
use Affilicious\Attribute\Domain\Model\Attribute\Value;
use Affilicious\Attribute\Domain\Model\AttributeGroup;
use Affilicious\Attribute\Domain\Model\AttributeGroupFactoryInterface;
use Affilicious\Attribute\Domain\Model\AttributeTemplateGroupId;
use Affilicious\Attribute\Domain\Model\AttributeTemplateGroupRepositoryInterface;
use Affilicious\Common\Domain\Model\Key;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Infrastructure\Repository\Carbon\CarbonProductRepository;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class InMemoryAttributeGroupFactory implements AttributeGroupFactoryInterface
{
    /**
     * @var AttributeTemplateGroupRepositoryInterface
     */
    private $attributeTemplateGroupRepository;

    /**
     * @since 0.6
     * @param AttributeTemplateGroupRepositoryInterface $attributeTemplateGroupRepository
     */
    public function __construct(AttributeTemplateGroupRepositoryInterface $attributeTemplateGroupRepository)
    {
        $this->attributeTemplateGroupRepository = $attributeTemplateGroupRepository;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function create(Title $title, Name $name, Key $key)
    {
        $attributeGroup = new AttributeGroup($title, $name, $key);

        return $attributeGroup;
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function createFromTemplateIdAndData(AttributeTemplateGroupId $attributeTemplateGroupId, $data)
    {
        $attributeTemplateGroup = $this->attributeTemplateGroupRepository->findById($attributeTemplateGroupId);
        if($attributeTemplateGroup === null || !is_array($data)) {
            return null;
        }

        $attributeGroup = $this->create(
            $attributeTemplateGroup->getTitle(),
            $attributeTemplateGroup->getName(),
            $attributeTemplateGroup->getKey()
        );

        $attributeGroup->setTemplateId($attributeTemplateGroupId);

        $attributeTemplates = $attributeTemplateGroup->getAttributeTemplates();
        foreach ($attributeTemplates as $index => $attributeTemplate) {
            if(!isset($data[$index])) {
                return null;
            }

            $rawAttribute = $data[$index];
            if(empty($rawAttribute)) {
                return null;
            }

            $customValue = $rawAttribute[CarbonProductRepository::VARIANT_ATTRIBUTES_CUSTOM_VALUE];
            $customValue = $attributeTemplate->getType()->isEqualTo(Type::number()) ? floatval($customValue) : $customValue;

            $attribute = new Attribute(
                $attributeTemplate->getTitle(),
                $attributeTemplate->getName(),
                $attributeTemplate->getKey(),
                new Type($attributeTemplate->getType()->getValue()),
                new Value($customValue)
            );

            if($attributeTemplate->hasUnit()) {
                $attribute->setUnit(new Unit($attributeTemplate->getUnit()->getValue()));
            }

            $attributeGroup->addAttribute($attribute);
        }

        return $attributeGroup;
    }
}
