<?php

namespace BehatLauncher\Extension\Command;

use BehatLauncher\Application;
use Symfony\Component\Console\Command\Command as BaseCommand;

/**
 * Abstract class for Behat-Launcher commands.
 *
 * Provides utilities and shortcuts.
 */
abstract class AbstractCommand extends BaseCommand implements \ArrayAccess
{
    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;

        parent::__construct();
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->app[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        throw new \RuntimeException('Modification of application not allowed.');
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        throw new \RuntimeException('Modification of application not allowed.');
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->app[$offset]);
    }
}
