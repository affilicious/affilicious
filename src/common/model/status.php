<?php
namespace Affilicious\Common\Model;

use Webmozart\Assert\Assert;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @see https://codex.wordpress.org/Post_Status
 */
class Status
{
    use Simple_Value_Trait {
        Simple_Value_Trait::__construct as private set_value;
    }

    const PUBLISH = 'publish';
    const FUTURE = 'future';
    const DRAFT = 'draft';
    const PENDING = 'pending';
    const _PRIVATE = 'private';
    const TRASH = 'trash';
    const AUTO_DRAFT = 'auto-draft';
    const INHERIT = 'inherit';

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
        Assert::string($value, 'The status must be a string. Got: %s');

		$this->set_value($value);
	}
}
