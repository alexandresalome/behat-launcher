<?php

use Behat\Behat\Context\BehatContext;

class FeatureContext extends BehatContext
{
    /**
     * @When /^step (\d+) is executed$/
     */
    public function step($count)
    {
        sleep(1);
    }
}
