<?php
namespace Affilicious\Common\Model;

use Affilicious\Common\Helper\Assert_Helper;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @see https://codex.wordpress.org/Post_Status
 * @since 0.9
 */
class Status
{
    use Simple_Value_Trait {
        Simple_Value_Trait::__construct as private set_value;
    }

	/**
	 * @since 0.9
	 * @var string
	 */
    const PUBLISH = 'publish';

	/**
	 * @since 0.9
	 * @var string
	 */
    const FUTURE = 'future';

	/**
	 * @since 0.9
	 * @var string
	 */
    const DRAFT = 'draft';

	/**
	 * @since 0.9
	 * @var string
	 */
    const PENDING = 'pending';

	/**
	 * @since 0.9
	 * @var string
	 */
    const _PRIVATE = 'private';

	/**
	 * @since 0.9
	 * @var string
	 */
    const TRASH = 'trash';

	/**
	 * @since 0.9
	 * @var string
	 */
    const AUTO_DRAFT = 'auto-draft';

	/**
	 * @since 0.9
	 * @var string
	 */
    const INHERIT = 'inherit';

	/**
	 * @since 0.9
	 * @var array
	 */
    public static $all = [
        self::PUBLISH,
	    self::FUTURE,
	    self::DRAFT,
	    self::PENDING,
	    self::_PRIVATE,
	    self::TRASH,
	    self::AUTO_DRAFT,
	    self::INHERIT
    ];

    /**
     * Viewable by everyone.
     *
     * @since 0.9
     * @return Status
     */
    public static function publish()
    {
        return new self(self::PUBLISH);
    }

    /**
     * Scheduled to be published in a future date.
     *
     * @since 0.9
     * @return Status
     */
    public static function future()
    {
        return new self(self::FUTURE);
    }

    /**
     * Incomplete post viewable by anyone with proper user role.
     *
     * @since 0.9
     * @return Status
     */
    public static function draft()
    {
        return new self(self::DRAFT);
    }

    /**
     * Awaiting a user with the publish_posts capability (typically a user assigned the Editor role) to publish.
     *
     * @since 0.9
     * @return Status
     */
    public static function pending()
    {
        return new self(self::PENDING);
    }

    /**
     * Viewable only to WordPress users at Administrator level.
     * Note that private is a reserved keyword in PHP.
     *
     * @since 0.9
     * @return Status
     */
    public static function _private()
    {
        return new self(self::_PRIVATE);
    }

    /**
     * Posts in the Trash are assigned the trash status.
     *
     * @since 0.9
     * @return Status
     */
    public static function trash()
    {
        return new self(self::TRASH);
    }

    /**
     * Revisions that WordPress saves automatically while you are editing.
     *
     * @since 0.9
     * @return Status
     */
    public static function auto_draft()
    {
        return new self(self::AUTO_DRAFT);
    }

    /**
     * Used with a child post (such as Attachments and Revisions) to determine the actual status from the parent post.
     *
     * @since 0.9
     * @return Status
     */
    public static function inherit()
    {
        return new self(self::INHERIT);
    }

	/**
	 * @inheritdoc
	 * @since 0.9
	 */
	public function __construct($value)
	{
		Assert_Helper::is_string_not_empty($value, __METHOD__, 'The status must be a string. Got: %s', '0.9.2');

		$this->set_value($value);
	}

	/**
	 * Get the translated label.
	 *
	 * @since 0.9.2
	 * @return null|string The translated label if any.
	 */
	public function get_label()
	{
		switch ($this->value) {
			case self::PUBLISH:
				$label = __('Publish', 'affilicious');
				break;
			case self::FUTURE:
				$label = __('Future', 'affilicious');
				break;
			case self::DRAFT:
				$label = __('Draft', 'affilicious');
				break;
			case self::PENDING:
				$label = __('Pending', 'affilicious');
				break;
			case self::_PRIVATE:
				$label = __('Private', 'affilicious');
				break;
			case self::TRASH:
				$label = __('Trash', 'affilicious');
				break;
			case self::AUTO_DRAFT:
				$label = __('Auto draft', 'affilicious');
				break;
			case self::INHERIT:
				$label = __('Inherit', 'affilicious');
				break;
			default:
				$label = null;
		}

		$label = apply_filters('aff_common_status_label', $label, $this->value);

		return $label;
	}
}
