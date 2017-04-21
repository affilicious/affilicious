<?php
namespace StephenHarris\WordPressBehatExtension\Context\Options;

use Behat\Behat\Context\Context;

class WordPressOptionContext implements Context
{

    /**
     * @Given I set :option option to :value
     */
    public function iSetOptionTo($option, $value)
    {
        update_option($option, $value);
    }

    /**
     * @Then I the :option option should be set to :value
     */
    public function theOptionShouldBeSetTo($option, $value)
    {
        $actual = get_option($option);

        if ($actual != $value) {
            throw new \Exception(sprintf('Expected option to be set to "%s". Found "%s"', $value, $actual));
        }
    }
}
