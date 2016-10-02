<?php
namespace Affilicious\Detail\Infrastructure\Persistence\Carbon;

use Affilicious\Common\Domain\Exception\InvalidPostTypeException;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Detail\Domain\Model\Detail\Detail;
use Affilicious\Detail\Domain\Model\Detail\DetailFactoryInterface;
use Affilicious\Detail\Domain\Model\Detail\HelpText;
use Affilicious\Detail\Domain\Model\Detail\Type;
use Affilicious\Detail\Domain\Model\Detail\Unit;
use Affilicious\Detail\Domain\Model\DetailGroup;
use Affilicious\Detail\Domain\Model\DetailGroupFactoryInterface;
use Affilicious\Detail\Domain\Model\DetailGroupId;
use Affilicious\Detail\Domain\Model\DetailGroupRepositoryInterface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class CarbonDetailGroupRepository implements DetailGroupRepositoryInterface
{
    //TODO: Convert the database structure from name to title
    const DETAILS = 'affilicious_detail_group_fields';
    const DETAIL_TITLE = 'name';
    const DETAIL_TYPE = 'type';
    const DETAIL_UNIT = 'unit';
    const DETAIL_HELP_TEXT = 'help_text';

    /**
     * @var DetailGroupFactoryInterface
     */
    protected $detailGroupFactory;

    /**
     * @var DetailFactoryInterface
     */
    protected $detailFactory;

    /**
     * @since 0.6
     * @param DetailGroupFactoryInterface $detailGroupFactory
     * @param DetailFactoryInterface $detailFactory
     */
    public function __construct(
        DetailGroupFactoryInterface $detailGroupFactory,
        DetailFactoryInterface $detailFactory
    )
    {
        $this->detailGroupFactory = $detailGroupFactory;
        $this->detailFactory = $detailFactory;
    }

    /**
     * @inheritdoc
     */
    public function findById(DetailGroupId $detailGroupId)
    {
        $post = get_post($detailGroupId->getValue());
        if ($post === null) {
            return null;
        }

        $detailGroup = $this->buildDetailGroupFromPost($post);
        return $detailGroup;
    }

    /**
     * @inheritdoc
     */
    public function findAll()
    {
        $query = new \WP_Query(array(
            'post_type' => DetailGroup::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ));

        $detailGroups = array();
        if($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $detailGroup = self::buildDetailGroupFromPost($query->post);
                $detailGroups[] = $detailGroup;
            }

            wp_reset_postdata();
        }

        return $detailGroups;
    }

    /**
     * Convert the post into a detail group
     *
     * @since 0.3
     * @param \WP_Post $post
     * @return DetailGroup
     */
    protected function buildDetailGroupFromPost(\WP_Post $post)
    {
        if($post->post_type !== DetailGroup::POST_TYPE) {
            throw new InvalidPostTypeException($post->post_type, DetailGroup::POST_TYPE);
        }

        // Title, Name, Key
        $detailGroup = $this->detailGroupFactory->create(
            new Title($post->post_title),
            new Name($post->post_name)
        );

        // ID
        $detailGroup->setId(new DetailGroupId($post->ID));

        // Details
        $detailGroup = $this->addDetails($detailGroup);

        return $detailGroup;
    }

    /**
     * Add the details to the detail group
     *
     * @since 0.6
     * @param DetailGroup $detailGroup
     * @return DetailGroup
     */
    protected function addDetails(DetailGroup $detailGroup)
    {
        $rawDetails = carbon_get_post_meta($detailGroup->getId()->getValue(), self::DETAILS, 'complex');
        if (!empty($rawDetails)) {
            foreach ($rawDetails as $rawDetail) {
                $detail = $this->buildDetailFromArray($rawDetail);

                if(!empty($detail)) {
                    $detailGroup->addDetail($detail);
                }
            }
        }

        return $detailGroup;
    }

    /**
     * Build the detail from the array
     *
     * @since 0.6
     * @param array $rawDetail
     * @return null|Detail
     */
    protected function buildDetailFromArray(array $rawDetail)
    {
        $title = isset($rawDetail[self::DETAIL_TITLE]) ? $rawDetail[self::DETAIL_TITLE] : null;
        $type = isset($rawDetail[self::DETAIL_TYPE]) ? $rawDetail[self::DETAIL_TYPE] : null;
        $unit = isset($rawDetail[self::DETAIL_UNIT]) ? $rawDetail[self::DETAIL_UNIT] : null;
        $helpText = isset($rawDetail[self::DETAIL_HELP_TEXT]) ? $rawDetail[self::DETAIL_HELP_TEXT] : null;

        if(empty($title) || empty($type)) {
            return null;
        }

        $detail = $this->detailFactory->create(
            new Title($title),
            new Type($type)
        );

        if(!empty($unit)) {
            $detail->setUnit(new Unit($unit));
        }

        if(!empty($helpText)) {
            $detail->setHelpText(new HelpText($helpText));
        }

        return $detail;
    }
}
