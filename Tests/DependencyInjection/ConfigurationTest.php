<?php

namespace Alex\BehatLauncherBundle\Tests\DependencyInjection;

use Alex\BehatLauncherBundle\DependencyInjection\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testEmpty()
    {
        $actual = array();
        $expected = array(
            'connection_id' => 'doctrine.dbal.default_connection',
            'projects' => array(
                'Default' => array(
                    'path' => '.',
                    'properties' => array()
                )
            )
        );
        $this->assertEquals($expected, $this->processConfiguration($actual));
    }

    public function testNameAttribute()
    {
        $actual = array('projects' => array(array('name' => 'foo', 'path' => 'bar')));
        $expected = array(
            'connection_id' => 'doctrine.dbal.default_connection',
            'projects' => array(
                'foo' => array(
                    'path' => 'bar',
                    'properties' => array()
                )
            )
        );
        $this->assertEquals($expected, $this->processConfiguration($actual));
    }

    private function processConfiguration(array $config)
    {
        $conf = new Configuration();
        $tree = $conf->getConfigTreeBuilder()->buildTree();

        $config = $tree->normalize($config);
        return $tree->finalize($config);
    }
}
