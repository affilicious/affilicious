<?php

/**
 * Fake sending email. In fact just write a file to the filesystem, so
 * a test service can read it.
 *
 * @param string|array $to Array or comma-separated list of email addresses to send message.
 * @param string $subject Email subject
 * @param string $message Message contents
 *
 * @return bool True if the email got sent (i.e. if the fake email file was written)
 */
function wp_mail($recipients, $subject, $message, $headers = '', $attachments = array())
{
    $recipients = is_array($recipients) ? $recipients : explode(',', $recipients);
    $recipients = array_map('trim', $recipients);

    foreach ($recipients as $recipient) {
        $file_name = sanitize_file_name(time() . "-$recipient-" . sanitize_title_with_dashes($subject));
        $file_path = trailingslashit(WORDPRESS_FAKE_MAIL_DIR) . $file_name;

        $data = array(
            'to'          => $recipient,
            'subject'     => $subject,
            'message'     => $message,
            'headers'     => $headers,
            'attachments' => $attachments,
        );

        $result = (bool) file_put_contents($file_path, json_encode($data));

        if (! $result) {
            throw new \Exception(sprintf('Unable to send e-mail with subject "%s" to %s', $subject, $recipient));
        }
    }

    return true;
}
