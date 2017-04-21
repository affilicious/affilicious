<?php

namespace StephenHarris\WordPressBehatExtension\Context\Page\Element;

use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use Behat\Mink\Exception\UnsupportedDriverActionException;

class AdminMenu extends Element
{
    use \StephenHarris\WordPressBehatExtension\StripHtml;
    /**
     * @var array|string $selector
     */
    protected $selector = '#adminmenu';

    public function getTopLevelMenuItems()
    {

        $menuItemNodes = $this->findAll('css', '#adminmenu > li a .wp-menu-name');
        $menuItemTexts = array();

        foreach ($menuItemNodes as $n => $element) {
            $menuItemTexts[] = $this->stripTagsAndContent($element->getHtml());
        }

        return $menuItemTexts;
    }

    public function clickMenuItem($item)
    {

        $item = array_map('trim', preg_split('/(?<!\\\\)>/', $item));
        $click_node = false;

        $first_level_items = $this->findAll('css', 'li.menu-top');

        foreach ($first_level_items as $first_level_item) {
            //We use getHtml and strip the tags, as `.wp-menu-name` might not be visible (i.e. when the menu is
            // collapsed) so getText() will be empty.
            //@link https://github.com/stephenharris/WordPressBehatExtension/issues/2
            $itemName = $this->stripTagsAndContent($first_level_item->find('css', '.wp-menu-name')->getHtml());

            if (strtolower($item[0]) == strtolower($itemName)) {
                if (isset($item[1])) {
                    $second_level_items = $first_level_item->findAll('css', 'ul li a');

                    foreach ($second_level_items as $second_level_item) {
                        $itemName = $this->stripTagsAndContent($second_level_item->getHtml());
                        if (strtolower($item[1]) == strtolower($itemName)) {
                            try {
                                //Focus on the menu link so the submenu appears
                                $first_level_item->find('css', 'a.menu-top')->focus();
                            } catch (UnsupportedDriverActionException $e) {
                                //This will fail for GoutteDriver but neither is it necessary
                            }
                            $click_node = $second_level_item;
                            break;
                        }
                    }
                } else {
                    //We are clicking a top-level item:
                    $click_node = $first_level_item->find('css', 'a');
                }
                break;
            }
        }

        if (false === $click_node) {
            throw new \Exception('Menu item could not be found');
        }

        $click_node->click();
    }
}
