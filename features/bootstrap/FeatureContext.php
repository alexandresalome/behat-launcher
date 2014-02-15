<?php

use Behat\Behat\Context\BehatContext;
use Symfony\Component\Process\ProcessBuilder;
use WebDriver\Behat\WebDriverContext;

class FeatureContext extends BehatContext
{
    const CONFIG_HEADER = "\n// projects for testing are added below this line. Any project you add might be deleted.\n";

    public function __construct()
    {
        $this->useContext('webdriver', new WebDriverContext());
    }

    /**
     * @When /^I purge runs$/
     *
     * @beforeScenario
     */
    public function iPurgeRuns()
    {
        $this->runConsole(array('purge'));
    }

    /**
     * @Given /^test project "([^"]+)"$/
     */
    public function testProject($name)
    {
        $configFile = __DIR__.'/../../config.php';

        if (!file_exists($configFile)) {
            throw new \RuntimeException('No config file found in project.');
        }

        $content = file_get_contents($configFile);

        $path = realpath(__DIR__.'/../../test_projects/'.$name);
        if (!$path) {
            throw new \InvalidArgumentException(sprintf('Found no test project named "%s".', $name));
        }

        $behatBin = realpath(__DIR__.'/../../vendor/behat/behat/bin/behat');

        $testContent = '$app->createProject("TEST-'.$name.'", "'.$path.'")->setBehatBin("'.$behatBin.'");';

        if (false === $pos = strpos($content, self::CONFIG_HEADER)) {
            $content = $content.self::CONFIG_HEADER.$testContent;
        } else {
            $content = substr($content, 0, $pos).self::CONFIG_HEADER.$testContent;
        }

        file_put_contents($configFile, $content);
    }

    /**
     * @When /^I run all units$/
     */
    public function iRunAllUnits()
    {
        $this->runConsole(array('run', '--stop-on-finish'));
    }

    private function runConsole(array $args)
    {
        $process = ProcessBuilder::create(array_merge(array('php', 'behat-launcher'), $args))
            ->setWorkingDirectory(__DIR__.'/../..')
            ->setTimeout(null)
            ->getProcess()
        ;

        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException(sprintf('Error while executing "%s": %s', $process->getCommandLine(), $process->getOutput().$process->getErrorOutput()));
        }
    }
}
