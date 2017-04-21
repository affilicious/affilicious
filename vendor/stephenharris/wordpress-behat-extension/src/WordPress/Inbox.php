<?php
namespace StephenHarris\WordPressBehatExtension\WordPress;

/**
 * An inbox is a collection of e-mails sent to a given e-mail
 *
 * The emails themselves live in JSON files stored in the directory passed
 * to the Inbox's constructor.
 *
 * You should not instantiate an Inbox directly, but use InboxFactory
 *
 * @package StephenHarris\WordPressBehatExtension\WordPress
 */
class Inbox
{
    
    /**
     * The e-mail address associated with this inbox
     * @var string
     */
    private $emailAddress;
    
    /**
     * An array of StephenHarris\WordPressBehatExtension\WordPress\Email objects
     * @var array
     */
    private $emails;

    /**
     * Will set up an inbox with all recorded emails sent to $emailAddress
     *
     * You should not instantiate an Inbox directly, but use InboxFactory
     *
     * @private
     * @param string $emailAddress The e-mail address of the recipient.
     * @param string $dir The location to store the e-mails at
     */
    public function __construct($emailAddress, $dir)
    {
        $this->emailAddress = $emailAddress;
        $this->dir = $dir;
        $this->refresh();
    }
    
    /**
     * Returns an array of e-mails in this inbox
     */
    public function getEmails()
    {
        return $this->emails;
    }
    
    /**
     * Return the latest e-mail recieved with a given subject.
     *
     * If no subject is present then the latest e-mail recieved is returned.
     *
     * @param string|null $subject
     * @return \StephenHarris\WordPressBehatExtension\WordPress\Email
     */
    public function getLatestEmail($subject = null)
    {
        
        if (empty($this->emails)) {
            throw new \Exception(sprintf("Inbox for %s is empty", $this->emailAddress));
        }

        $this->refresh();

        foreach ($this->emails as $email) {
            if (is_null($subject) || $subject == $email->getSubject()) {
                return $email;
            }
        }
                
        throw new \Exception(sprintf("No emails for %s found with subject '%s'", $this->emailAddress, $subject));
    }
    
    /**
     * Create an e-mail instance from a file
     *
     * The content of a file encodes details of the file.
     * @param string $file
     */
    protected function parseMail($file)
    {
        $fileContents = json_decode(file_get_contents($file), true);

        if (is_null($fileContents)) {
            throw new \Exception(sprintf('Invalid file format of %s. Expected json', $file));
        }
        $titleParts   = explode('-', basename($file)); //timestamp, email, subject
        $timestamp    = $titleParts[0];

        return new Email($fileContents['to'], $fileContents['subject'], $fileContents['message'], $timestamp);
    }
        
    /**
     * Delete all e-mails in this inbox
     */
    public function clearInbox()
    {
        $filePattern = $this->getInboxDirectory() . '*' . $this->emailAddress . '*';
        foreach (glob($filePattern) as $email) {
            unlink($email);
        }
        $this->emails = array();
    }
    
    public function refresh()
    {
        $filePattern = $this->getInboxDirectory() . '*' . $this->emailAddress . '*';
        $this->emails = array();
        foreach (glob($filePattern) as $file) {
            $this->emails[] = $this->parseMail($file);
        }

        usort($this->emails, function ($email1, $email2) {
            //sort by timestamp, descending
            return $email2->getTimestamp() - $email1->getTimestamp();
        });
        
        return $this;
    }

    protected function getInboxDirectory()
    {
        return rtrim($this->dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }
}
