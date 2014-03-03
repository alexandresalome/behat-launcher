<?php

use Alex\BehatLauncher\Application;
use Alex\BehatLauncher\Behat\Run;
use Alex\BehatLauncher\Behat\RunUnit;
use Behat\Behat\Context\Step\When;
use Behat\Gherkin\Node\TableNode;
use Symfony\Component\Process\ProcessBuilder;
use WebDriver\Behat\AbstractWebDriverContext;
use WebDriver\Behat\WebDriverContext;

class FeatureContext extends AbstractWebDriverContext
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

        $testContent = strtr(file_get_contents($path.'/config'), array(
            '$name'     => var_export('TEST-'.$name, true),
            '$path'     => var_export($path, true),
            '$behatBin' => var_export($behatBin, true),
        ));

        if (false === $pos = strpos($content, self::CONFIG_HEADER)) {
            $content = $content.self::CONFIG_HEADER.$testContent;
        } else {
            $content = substr($content, 0, $pos).self::CONFIG_HEADER.$testContent;
        }

        file_put_contents($configFile, $content);
    }

    /**
     * @Given /^following run for "([^"]+)":$/
     */
    public function followingRunFor($name, TableNode $table)
    {
        $app = $this->getApplication();

        $project = $app['project_list']->get($name);

        $methods = array(
            'created_at' => function (RunUnit $unit, $val) {
                $int = new \DateInterval($val);
                $now = new \DateTime();
                $unit->setCreatedAt($now->sub($int));
            },
            'started_at' => function (RunUnit $unit, $val) {
                $int = new \DateInterval($val);
                $now = new \DateTime();
                $unit->setStartedAt($now->sub($int));
            },
            'finished_at' => function (RunUnit $unit, $val) {
                $int = new \DateInterval($val);
                $now = new \DateTime();
                if (!$unit->getStartedAt()) {
                    $unit->setStartedAt($now->sub($int));
                }
                $unit->setFinishedAt($now->sub($int));
            },
            'feature' => function (RunUnit $unit, $val) {
                $unit->setFeature($val);
            },
            'return_code' => function (RunUnit $unit, $val) {
                $unit->setReturnCode($val);
            },
        );

        $headers = $table->getRow(0);
        foreach ($headers as $col) {
            if (!isset($methods[$col])) {
                throw new \RuntimeException(sprintf('No handler for column "%s".', $col));
            }
        }

        $run = new Run();
        $run
            ->setProjectName($name)
        ;

        $units = $run->getUnits();

        foreach ($table->getRows() as $i => $row) {
            if ($i == 0) {
                continue;
            }

            $unit = new RunUnit();
            foreach ($headers as $i => $header) {
                $value = $row[$i];
                if ($value === '@null') {
                    continue;
                }

                $methods[$header]($unit, $row[$i]);
            }

            $units->add($unit);
        }

        $app['run_storage']->saveRun($run);
    }

    /**
     * @When /^I run all units$/
     */
    public function iRunAllUnits()
    {
        $this->runConsole(array('run', '--stop-on-finish'));
    }

    /**
     * @When /^I start all units$/
     */
    public function iStartAllUnits()
    {
        $this->runConsole(array('run', '--stop-on-finish'), false);
    }

    /**
     * @Given /^I look into output frame$/
     */
    public function iLookIntoOutputFrame()
    {
        $this->getBrowser()->request('POST', 'frame', json_encode(array('id' => 'output-file')));
    }

    /**
     * @Given /^I look into main frame$/
     */
    public function iLookIntoMainFrame()
    {
        $this->getBrowser()->request('POST', 'frame', json_encode(array('id' => null)));
        return array(
            new When('I click on "Close output"')
        );
    }

    private function runConsole(array $args, $block = true)
    {
        $process = ProcessBuilder::create(array_merge(array('php', 'behat-launcher'), $args))
            ->setWorkingDirectory(__DIR__.'/../..')
            ->setTimeout(null)
            ->getProcess()
        ;

        if ($block) {
            $process->run();

            if (!$process->isSuccessful()) {
                throw new \RuntimeException(sprintf('Error while executing "%s": %s', $process->getCommandLine(), $process->getOutput().$process->getErrorOutput()));
            }
        } else {
            $process->start();
        }
    }

    private function getApplication()
    {
        $app = new Application();
        require __DIR__.'/../../config.php';

        return $app;
    }
}
