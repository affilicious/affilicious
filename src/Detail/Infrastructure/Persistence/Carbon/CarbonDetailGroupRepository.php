<?php
namespace Affilicious\Detail\Infrastructure\Persistence\Carbon;

use Affilicious\Common\Application\Helper\DatabaseHelper;
use Affilicious\Common\Domain\Exception\InvalidPostTypeException;
use Affilicious\Detail\Domain\Model\Detail\Detail;
use Affilicious\Detail\Domain\Model\Detail\HelpText;
use Affilicious\Detail\Domain\Model\Detail\Key;
use Affilicious\Detail\Domain\Model\Detail\Name;
use Affilicious\Detail\Domain\Model\Detail\Type;
use Affilicious\Detail\Domain\Model\Detail\Unit;
use Affilicious\Detail\Domain\Model\DetailGroup;
use Affilicious\Detail\Domain\Model\DetailGroupId;
use Affilicious\Detail\Domain\Model\DetailGroupRepositoryInterface;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class CarbonDetailGroupRepository implements DetailGroupRepositoryInterface
{
    const CARBON_DETAILS = 'affilicious_detail_group_fields';
    const CARBON_DETAIL_NAME = 'name';
    const CARBON_DETAIL_TYPE = 'type';
    const CARBON_DETAIL_UNIT = 'unit';
    const CARBON_DETAIL_HELP_TEXT = 'help_text';

    /**
     * @inheritdoc
     */
    public function findById(DetailGroupId $detailGroupId)
    {
        $post = get_post($detailGroupId->getValue());
        if ($post === null) {
            return null;
        }

        $detailGroup = $this->fromPost($post);
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
                $detailGroup = self::fromPost($query->post);
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
    private function fromPost(\WP_Post $post)
    {
        if($post->post_type !== DetailGroup::POST_TYPE) {
            throw new InvalidPostTypeException($post->post_type, DetailGroup::POST_TYPE);
        }

        $detailGroup = new DetailGroup($post);

        $fields = carbon_get_post_meta($detailGroup->getId()->getValue(), self::CARBON_DETAILS, 'complex');
        if (!empty($fields)) {
            foreach ($fields as $field) {
                $name = $field[self::CARBON_DETAIL_NAME];
                $key = DatabaseHelper::convertTextToKey($name);
                $type = $field[self::CARBON_DETAIL_TYPE];
                $unit = $field[self::CARBON_DETAIL_UNIT];
                $helpText = $field[self::CARBON_DETAIL_HELP_TEXT];

                $detail = new Detail(new Key($key), new Name($name), new Type($type));
                if(!empty($unit)) {
                    $detail->setUnit(new Unit($unit));
                }

                if(!empty($helpText)) {
                    $detail->setHelpText(new HelpText($helpText));
                }

                $detailGroup->addDetail($detail);
            }
        }

        return $detailGroup;
    }
}
