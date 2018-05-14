<?php
namespace Affilicious\Product\Listener;

use Affilicious\Common\Helper\Network_Helper;
use Affilicious\Product\Update\Update_Semaphore;
use Affilicious\Product\Update\Update_Timer;

if (!defined('ABSPATH')) {
	exit('Not allowed to access pages directly.');
}

/**
 * @since 0.9.20
 */
class Create_Blog_Listener
{
	/**
	 * @since 0.9.20
	 * @var Update_Semaphore
	 */
	protected $update_semaphore;

	/**
	 * @since 0.9.20
	 * @var Update_Timer
	 */
	protected $update_timer;

	/**
	 * @since 0.9.20
	 * @param Update_Semaphore $update_semaphore
	 * @param Update_Timer $update_timer
	 */
	public function __construct(Update_Semaphore $update_semaphore, Update_Timer $update_timer)
	{
		$this->update_semaphore = $update_semaphore;
		$this->update_timer = $update_timer;
	}

	/**
	 * This listener is called when a new blog is created in a multisite.
	 *
	 * @since 0.9.20
	 * @hook wpmu_new_blog
	 * @param int $blog_id The newly created ID of the blog in the network.
	 */
	public function listen($blog_id)
	{
		if (is_plugin_active_for_network('affilicious/affilicious.php')) {
			Network_Helper::for_blog($blog_id, function() {
				$this->update_semaphore->install();
				$this->update_timer->activate();
			});
		}
	}
}
