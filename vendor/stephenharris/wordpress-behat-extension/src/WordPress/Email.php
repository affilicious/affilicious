<?php
namespace StephenHarris\WordPressBehatExtension\WordPress;

/**
 * An e-mail instance represents a sent e-mail
 *
 * @package StephenHarris\WordPressBehatExtension\WordPress
 */
class Email
{

    private $recipient;
    
    private $subject;
    
    private $body;

    private $timestamp;
    
    public function __construct($recipient, $subject = '', $body = '', $timestamp = null)
    {
        $this->recipient = $recipient;
        $this->subject   = $subject;
        $this->body      = $body;
        $this->timestamp = is_null($timestamp) ? time() : $timestamp;
    }
    
    public function getRecipient()
    {
        return $this->recipient;
    }
    
    public function getSubject()
    {
        return $this->subject;
    }

    public function getBody()
    {
        return $this->body;
    }
    
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
