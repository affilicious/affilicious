<?php

namespace StephenHarris\WordPressBehatExtension\Context\Page\Element;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;

/**
 * Decorates a node with 'notice' class
 */
class Notice extends NodeElement
{
    use \StephenHarris\WordPressBehatExtension\StripHtml;

    const NOTICE_CLASS = 'notice';

    public function __construct(NodeElement $node, Session $session)
    {
        if (!$node->hasClass(self::NOTICE_CLASS)) {
            throw new \InvalidArgumentException(sprintf('Provided node does not have class %s', self::NOTICE_CLASS));
        }
        parent::__construct($node->getXpath(), $session);
    }

    public function assertHasText($text)
    {
        if ($text != $this->getText()) {
            throw new \Exception(sprintf(
                'Expected notice to contain text "%s". Found: "%s"',
                $text,
                $this->getText()
            ));
        }
    }

    public function isErrorNotice()
    {
        return $this->hasClass(NoticeType::ERROR);
    }

    public function isWarningNotice()
    {
        return $this->hasClass(NoticeType::WARNING);
    }

    public function isInfoNotice()
    {
        return $this->hasClass(NoticeType::INFO);
    }

    public function isSuccessNotice()
    {
        return $this->hasClass(NoticeType::SUCCESS);
    }
}
