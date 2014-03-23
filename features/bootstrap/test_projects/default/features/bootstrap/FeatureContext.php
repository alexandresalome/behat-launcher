<?php

use Behat\Behat\Context\BehatContext;

class FeatureContext extends BehatContext
{
    private $counter;

    /**
     * @Given /^I restart counter$/
     */
    public function iRestartCounter()
    {
        $this->counter = 0;
    }

    /**
     * @When /^I add (\d+)$/
     */
    public function iAdd($count)
    {
        $this->counter += $count;
    }

    /**
     * @Then /^counter should be (\d+)$/
     */
    public function counterShouldBe($count)
    {
        if ($this->counter != $count) {
            throw new \RuntimeException(sprintf('Expected counter to be "%s", actual is "%s".', $count, $this->counter));
        }
    }
}
