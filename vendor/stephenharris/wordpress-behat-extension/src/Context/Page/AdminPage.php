<?php

namespace StephenHarris\WordPressBehatExtension\Context\Page;

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;

use StephenHarris\WordPressBehatExtension\Context\Page\Element\Notice;
use StephenHarris\WordPressBehatExtension\Context\Page\Element\NoticeType;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;

class AdminPage extends Page
{

    private $session;

    /**
     * A bit of a hack, we require the Session to create instances of Notice, so we store it here
     * @param NodeElement $node
     * @param Session $session
     */
    public function __construct(Session $session, Factory $factory, array $parameters = array())
    {
        parent::__construct($session, $factory, $parameters);
        $this->session = $session;
    }

    public function getHeaderText()
    {
        $header = $this->getHeaderElement();
        $header_text = $header->getText();
        $header_link = $header->find('css', 'a');

        //The page headers can often incude an 'add new link'. Strip that out of the header text.
        if ($header_link) {
            $header_text  = trim(str_replace($header_link->getText(), '', $header_text));
        }

        return $header_text;
    }

    public function assertHasHeader($expected)
    {
        $actual = $this->getHeaderText();
        if ($expected !== $actual) {
            throw new \Exception(sprintf('Expected page header "%s", found "%s".', $expected, $actual));
        }
    }

    private function getHeaderElement()
    {
        //h2s were used prior to 4.3/4 and h1s after
        //@see https://make.wordpress.org/core/2015/10/28/headings-hierarchy-changes-in-the-admin-screens/
        $header2     = $this->find('css', '.wrap > h2');
        $header1     = $this->find('css', '.wrap > h1');

        if ($header1) {
            return $header1;
        } elseif ($header2) {
            return $header2;
        }

        throw new \Exception('Header could not be found');
    }

    public function clickLinkInHeader($link)
    {
        $header = $this->getHeaderElement();
        $header->clickLink($link);
    }

    public function getMenu()
    {
        return $this->getElement('Admin menu');
    }


    public function assertContainsErrorNotice($errorMessage)
    {
        $this->assertContainsNotice($errorMessage, new NoticeType(NoticeType::ERROR));
    }

    public function assertContainsWarningNotice($warningmMessage)
    {
        $this->assertContainsNotice($warningmMessage, new NoticeType(NoticeType::WARNING));
    }

    public function assertContainsInfoNotice($infoMessage)
    {
        $this->assertContainsNotice($infoMessage, new NoticeType(NoticeType::INFO));
    }

    public function assertContainsSuccessNotice($successMessage)
    {
        $this->assertContainsNotice($successMessage, new NoticeType(NoticeType::SUCCESS));
    }

    protected function assertContainsNotice($noticeMessage, NoticeType $type)
    {

        $notices = $this->getNotices($type);

        if (!$notices) {
            throw new \Exception(sprintf('No %s notices found', $type->label()));
        }

        foreach ($notices as $notice) {
            try {
                $notice->assertHasText($noticeMessage);
                return true;
            } catch (\Exception $e) {
                /* do nothing */
            }
        }

        throw new \Exception(sprintf('No %s notice with text "%s" found', $type->label(), $noticeMessage));
    }

    public function getNotices(NoticeType $type)
    {
        $notices = array();
        $noticeNodes = $this->findAll('css', $type->cssSelector());
        foreach ($noticeNodes as $node) {
            $notices[] = new Notice($node, $this->session);
        }
        return $notices;
    }


    /**
     * Modified isOpen function which throws exceptions
     * @param array $urlParameters
     * @see https://github.com/sensiolabs/BehatPageObjectExtension/issues/57
     * @return boolean
     */
    public function isOpen(array $urlParameters = array())
    {
        $this->verify($urlParameters);
        return true;
    }
}
