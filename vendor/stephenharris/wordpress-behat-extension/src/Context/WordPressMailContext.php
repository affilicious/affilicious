<?php
namespace StephenHarris\WordPressBehatExtension\Context;

use StephenHarris\WordPressBehatExtension\WordPress\InboxFactory;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Contains steps relating to e-mails sent by WordPress
 */
class WordPressMailContext extends RawMinkContext implements
    Context,
    SnippetAcceptingContext,
    WordPressInboxFactoryAwareContext
{

    protected $inboxFactory;

    public function setInboxFactory(InboxFactory $factory)
    {
        $this->inboxFactory = $factory;
    }

    /**
     * @Then /^the latest email to ([^ ]+@[^ ]+) should contain "([^"]*)"$/
     */
    public function assertFakeEmailReceipt($emailAddress, $pattern)
    {
        $regex = $this->fixStepArgument($pattern);

        $inbox   = $this->inboxFactory->getInbox($emailAddress);
        $email   = $inbox->getLatestEmail();
        $body    = $email->getBody();

        \PHPUnit_Framework_Assert::assertRegExp(
            "/$regex/",
            $body,
            sprintf(
                'Did not find an email to %s which matched "%s". Found instead: %s – ',
                $emailAddress,
                $regex,
                $body
            )
        );
    }

    /**
     * @Given /^I follow the (\w+) URL in the latest email to ([^ ]+@[^ ]+)$/
     */
    public function followEmailUrl($ordinal, $emailAddress)
    {
        $inbox   = $this->inboxFactory->getInbox($emailAddress);
        $email   = $inbox->getLatestEmail();
        $body    = $email->getBody();

        preg_match_all(
            '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#',
            $body,
            $matches
        );
        
        $links = $matches[0];
        $ordinals = array(
            'first'   => 1,
            'second'  => 2,
            'third'   => 3,
            'fourth'  => 4,
            'fifth'   => 5,
            'sixth'   => 6,
            'seventh' => 7,
            'eighth'  => 8,
            'ninth'   => 9,
            'tenth'   => 10
        );
        $ordinal = strtolower($ordinal);
        if (! isset($ordinals[$ordinal])) {
            $message = sprintf(
                'Could not identify ordinal "%s" (n.b. we only go up to "tenth")',
                $ordinal
            );
            throw new \Behat\Mink\Exception\ExpectationException($message, $this->getSession());
        }
        $i = $ordinals[$ordinal];
        // Our array is zero indexed
        $i--;
        if (! isset($links[$i])) {
            $message = sprintf(
                'Could not find a %s link amongst: %s',
                $ordinal,
                implode(', ', $links)
            );
            throw new \Behat\Mink\Exception\ExpectationException($message, $this->getSession());
        }

        $this->getSession()->visit($links[$i]);
    }
    
    /**
     * Returns fixed step argument (with \\" replaced back to ")
     *
     * @param string $argument
     *
     * @return string
     */
    protected function fixStepArgument($argument)
    {
        return str_replace('\\"', '"', $argument);
    }
}
