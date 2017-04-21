<?php
namespace StephenHarris\WordPressBehatExtension\WordPress;

/**
 * Used to instantiates an inbox for a given e-mail address
 *
 * @package StephenHarris\WordPressBehatExtension\WordPress
 */
class InboxFactory
{

    private $inboxes = array();

    /**
     * @param $dir The directory where e-mails will be stored. This is passed to each Inbox created by this factory.
     */
    public function __construct($dir)
    {
        $this->dir = $dir;
    }

    /**
     * Will set up an inbox with all recorded emails sent to $emailAddress
     * @param string $emailAddress The e-mail address of the recipient.
     */
    public function getInbox($emailAddress)
    {
        if (!isset($this->inboxes[$emailAddress])) {
            $this->inboxes[$emailAddress] = new Inbox($emailAddress, $this->dir);
        }
        return $this->inboxes[$emailAddress]->refresh();
    }
}
