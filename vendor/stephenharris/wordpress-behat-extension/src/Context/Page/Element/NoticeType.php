<?php

namespace StephenHarris\WordPressBehatExtension\Context\Page\Element;

use MyCLabs\Enum\Enum;

/**
 * Decorates a node with 'notice' class
 */
class NoticeType extends Enum
{
    const ERROR = 'notice-error';
    const WARNING = 'notice-warning';
    const INFO = 'notice-info';
    const SUCCESS = 'notice-success';

    public function label()
    {
        return strtolower($this->getKey());
    }

    public function cssSelector()
    {
        return '.'.$this->getValue();
    }
}
