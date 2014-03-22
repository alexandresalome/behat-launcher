<?php

namespace Alex\BehatLauncher\Tests\Model;

use Alex\BehatLauncher\Model\ProjectProperty;

class ProjectPropertyTest extends \PHPUnit_Framework_TestCase
{
    public function testConfig()
    {
        $property = new ProjectProperty();

        $property->setConfig('foo');
        $actual   = array();
        $expected = array('foo' => 3);
        $config = $property->mergeConfig($actual, 3);
        $this->assertEquals($expected, $config);

        $property->setConfig('foo');
        $actual   = array('foo' => 1);
        $expected = array('foo' => 3);
        $config = $property->mergeConfig($actual, 3);
        $this->assertEquals($expected, $config);

        $property->setConfig('foo.bar');
        $actual   = array();
        $expected = array('foo' => array('bar' => 3));
        $config = $property->mergeConfig($actual, 3);
        $this->assertEquals($expected, $config);

        $property->setConfig('foo.bar');
        $actual   = array('foo' => array('bar' => 2, 'baz' => 3));
        $expected = array('foo' => array('bar' => 3, 'baz' => 3));
        $config = $property->mergeConfig($actual, 3);
        $this->assertEquals($expected, $config);
    }
}
