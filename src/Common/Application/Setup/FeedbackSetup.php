<?php
namespace Affilicious\Common\Application\Setup;

class FeedbackSetup implements SetupInterface
{
	const MENU_SLUG = 'feedback';
	const TO = 'feedback@affilicioustheme.de';

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		add_submenu_page(
			'edit.php?post_type=product',
			__('Feedback', 'affilicious'),
			__('Feedback', 'affilicious'),
			'manage_options',
			self::MENU_SLUG,
			array($this, 'render')
		);
	}

	/**
	 * @inheritdoc
	 */
	public function render()
	{
		$action  = esc_url($_SERVER['REQUEST_URI']);
		$subject = isset($_POST['subject']) ? esc_attr($_POST['subject']) : '';
		$name    = isset($_POST['name']) ? esc_attr($_POST['name']) : '';
		$email   = isset($_POST['email']) ? esc_attr($_POST['email']) : '';
		$message = isset($_POST['message']) ? esc_attr($_POST['message']) : '';

		?>
		<div class="wrap">
			<h1><?php _e('Feedback', 'affilicious'); ?></h1>
			<p><?php _e('Please fill out the fields below and click on submit. As an alternative, you can write your feedback to our german <a href="https://www.facebook.com/groups/1139863846078608/">facebook community</a>', 'affilicious'); ?></p>

			<?php if (!$this->sendEmail()): ?>
				<form method="post" id="feedback-form" action="<?php echo $action; ?>">
					<table class="form-table">
						<tbody>
						<tr>
							<th>
								<label for="email"><?php _e('Email', 'affilicious'); ?></label>
							</th>
							<td>
								<input type="text" name="email" class="regular-text" value="<?php echo $email; ?>"
								       size="100%" placeholder="<?php _e('Email', 'affilicious'); ?>"/>
							</td>
						</tr>
						<tr>
							<th>
								<label for="name"><?php _e('Name', 'affilicious'); ?></label>
							</th>
							<td>
								<input type="text" name="name" class="regular-text" value="<?php echo $name; ?>"
								       size="100%" placeholder="<?php _e('Name', 'affilicious'); ?>"/>
							</td>
						</tr>
						<tr>
							<th>
								<label for="subject"><?php _e('Subject', 'affilicious'); ?></label>
							</th>
							<td>
								<input type="text" name="subject" class="regular-text" value="<?php echo $subject; ?>"
								       size="100%" placeholder="<?php _e('Subject', 'affilicious'); ?>"/>
							</td>
						</tr>
						<tr>
							<th>
								<label for="message"><?php _e('Message', 'affilicious'); ?></label>
							</th>
							<td>
							<textarea name="message" class="regular-text"
							          placeholder="<?php _e('Message', 'affilicious'); ?>"
							          cols="50" rows="10"><?php echo $message; ?></textarea>
							</td>
						</tr>
						<tr>
							<td>
								<input class="button-primary" type="submit"
								       value="<?php _e('Submit', 'affilicious'); ?>">
							</td>
						</tr>
						</tbody>
					</table>

					<?php wp_nonce_field('feedback'); ?>
				</form>
			<?php endif; ?>

			<div class="clear"></div>
		</div>
		<?php

	}

	/**
	 * Send the email to the feedback team
	 *
	 * @since 0.3.3
	 */
	function sendEmail()
	{
		if ($_SERVER['REQUEST_METHOD'] === "POST" && check_admin_referer('feedback')) {
			if (!empty($_POST['email']) && !empty($_POST['name']) && !empty($_POST['subject']) && !empty($_POST['message'])) {

				$to      = self::TO;
				$email   = sanitize_text_field($_POST['email']);
				$name    = sanitize_text_field($_POST['name']);
				$subject = sanitize_text_field($_POST['subject']);
				$message = esc_textarea($_POST['message']);

				$msg =
					"<p><strong>" . __('Email', 'affilicious') . "</strong>: " . $email . "</p>" .
					"<p><strong>" . __('Name', 'affilicious') . "</strong>: " . $name . "</p>" .
					"<p><strong>" . __('Subject', 'affilicious') . "</strong>: " . $subject . "</p>" .
					"<p>" . $message . "</p>" .

					$headers = __('Feedback:', 'affilicious') . ' ' . $name . ' <' . $email . '>';

				add_filter('wp_mail_content_type', array($this, 'setEmailContentType'));

				if (wp_mail($to, __('Feedback:', 'affilicious') . ' ' . $subject, $msg, $headers)) {
					echo '<div class="feedback-success">';
					echo '<p>' . __('Thanks, your feedback has been sent! We will read every single feedback.', 'affilicious') . '</p>';
					echo "</div>";


				} else {
					echo '<div class="feedback-error">';
					echo '<p>' . __('Failed to send the email. Please try it again', 'affilicious') . '</p>';
					echo '</div>';

					return false;
				}

				unset($_POST['email'], $_POST['name'], $_POST['subject'], $_POST['message']);

				remove_filter('wp_mail_content_type', array($this, 'setEmailContentType'));

				return true;
			} else {
				echo '<div class="feedback-error">';
				echo '<p>' . __("Empty fields aren't allowed. Please fill out all fields.", 'affilicious') . '</p>';
				echo '</div>';

				return false;
			}
		}

		return false;
	}

	/**
	 * Set the html content type for the email
	 *
	 * @since 0.3.3
	 * @return string
	 */
	function setEmailContentType()
	{
		return 'text/html';
	}
}
