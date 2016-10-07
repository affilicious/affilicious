<?php
namespace Affilicious\Detail\Infrastructure\Persistence\Carbon;

use Affilicious\Common\Domain\Exception\InvalidPostTypeException;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Common\Infrastructure\Persistence\Carbon\AbstractCarbonRepository;
use Affilicious\Detail\Domain\Model\DetailTemplate\DetailTemplate;
use Affilicious\Detail\Domain\Model\DetailTemplate\DetailTemplateFactoryInterface;
use Affilicious\Detail\Domain\Model\DetailTemplate\HelpText;
use Affilicious\Detail\Domain\Model\DetailTemplate\Type;
use Affilicious\Detail\Domain\Model\DetailTemplate\Unit;
use Affilicious\Detail\Domain\Model\DetailTemplateGroup;
use Affilicious\Detail\Domain\Model\DetailTemplateGroupFactoryInterface;
use Affilicious\Detail\Domain\Model\DetailTemplateGroupId;
use Affilicious\Detail\Domain\Model\DetailTemplateGroupRepositoryInterface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class CarbonDetailTemplateGroupRepository extends AbstractCarbonRepository implements DetailTemplateGroupRepositoryInterface
{
    //TODO: Convert the database structure from name to title
    const DETAILS = 'affilicious_detail_group_fields';
    const DETAIL_TITLE = 'name';
    const DETAIL_TYPE = 'type';
    const DETAIL_UNIT = 'unit';
    const DETAIL_HELP_TEXT = 'help_text';

    /**
     * @var DetailTemplateGroupFactoryInterface
     */
    protected $detailTemplateGroupFactory;

    /**
     * @var DetailTemplateFactoryInterface
     */
    protected $detailTemplateFactory;

    /**
     * @since 0.6
     * @param DetailTemplateGroupFactoryInterface $detailTemplateGroupFactory
     * @param DetailTemplateFactoryInterface $detailTemplateFactory
     */
    public function __construct(
        DetailTemplateGroupFactoryInterface $detailTemplateGroupFactory,
        DetailTemplateFactoryInterface $detailTemplateFactory
    )
    {
        $this->detailTemplateGroupFactory = $detailTemplateGroupFactory;
        $this->detailTemplateFactory = $detailTemplateFactory;
    }

    /**
     * @inheritdoc
     */
    public function findById(DetailTemplateGroupId $detailTemplateGroupId)
    {
        $post = get_post($detailTemplateGroupId->getValue());
        if ($post === null) {
            return null;
        }

        $detailTemplateGroup = $this->buildDetailGroupFromPost($post);
        return $detailTemplateGroup;
    }

    /**
     * @inheritdoc
     */
    public function findAll()
    {
        $query = new \WP_Query(array(
            'post_type' => DetailTemplateGroup::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        $detailTemplateGroups = array();
        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $detailTemplateGroup = self::buildDetailGroupFromPost($query->post);
                $detailTemplateGroups[] = $detailTemplateGroup;
            }

            wp_reset_postdata();
        }

        return $detailTemplateGroups;
    }

    /**
     * Convert the post into a detail template group
     *
     * @since 0.6
     * @param \WP_Post $post
     * @return DetailTemplateGroup
     */
    protected function buildDetailGroupFromPost(\WP_Post $post)
    {
        if($post->post_type !== DetailTemplateGroup::POST_TYPE) {
            throw new InvalidPostTypeException($post->post_type, DetailTemplateGroup::POST_TYPE);
        }

        // Title, Name, Key
        $detailTemplateGroup = $this->detailTemplateGroupFactory->create(
            new Title($post->post_title),
            new Name($post->post_name)
        );

        // ID
        $detailTemplateGroup->setId(new DetailTemplateGroupId($post->ID));

        // Details
        $detailTemplateGroup = $this->addDetails($detailTemplateGroup);

        return $detailTemplateGroup;
    }

    /**
     * Add the detail templates to the detail template group
     *
     * @since 0.6
     * @param DetailTemplateGroup $detailGroup
     * @return DetailTemplateGroup
     */
    protected function addDetails(DetailTemplateGroup $detailGroup)
    {
        $rawDetailTemplates = carbon_get_post_meta($detailGroup->getId()->getValue(), self::DETAILS, 'complex');
        if (!empty($rawDetailTemplates)) {
            foreach ($rawDetailTemplates as $rawDetailTemplate) {
                $detail = $this->buildDetailTemplateFromArray($rawDetailTemplate);

                if(!empty($detail)) {
                    $detailGroup->addDetail($detail);
                }
            }
        }

        return $detailGroup;
    }

    /**
     * Build the detail template from the array
     *
     * @since 0.6
     * @param array $rawDetailTemplate
     * @return null|DetailTemplate
     */
    protected function buildDetailTemplateFromArray(array $rawDetailTemplate)
    {
        $title = isset($rawDetailTemplate[self::DETAIL_TITLE]) ? $rawDetailTemplate[self::DETAIL_TITLE] : null;
        $type = isset($rawDetailTemplate[self::DETAIL_TYPE]) ? $rawDetailTemplate[self::DETAIL_TYPE] : null;
        $unit = isset($rawDetailTemplate[self::DETAIL_UNIT]) ? $rawDetailTemplate[self::DETAIL_UNIT] : null;
        $helpText = isset($rawDetailTemplate[self::DETAIL_HELP_TEXT]) ? $rawDetailTemplate[self::DETAIL_HELP_TEXT] : null;

        if(empty($title) || empty($type)) {
            return null;
        }

        $detailTemplate = $this->detailTemplateFactory->create(
            new Title($title),
            new Type($type)
        );

        if(!empty($unit)) {
            $detailTemplate->setUnit(new Unit($unit));
        }

        if(!empty($helpText)) {
            $detailTemplate->setHelpText(new HelpText($helpText));
        }

        return $detailTemplate;
    }
}
