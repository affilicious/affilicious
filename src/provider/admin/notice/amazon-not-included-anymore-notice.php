<?php
namespace Affilicious\Provider\Admin\Notice;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.10
 */
class Amazon_Not_Included_Anymore_Notice
{
	/**
	 * @since 0.10
	 */
	const DISMISSIBLE_ID = 'amazon_not_included_anymore';

	/**
	 * @since 0.10
	 */
	public function render()
	{
		if(aff_is_notice_dismissed(self::DISMISSIBLE_ID)) {
			return;
		}

		aff_render_template('admin/notice/error-notice', [
			'dismissible_id' => self::DISMISSIBLE_ID,
			'message' => __('"Amazon Import And Update" is not included in Affilicious by default anymore and was outsourced into a <a href="https://affilicious.de/downloads/amazon-import-und-update/" target="_blank">separate addon</a>. Contact the support to check if you can get a multi license for FREE. If you have already got a license, you can ignore this notice.', 'affilicious'),
		]);
	}
}
