<?php

namespace BehatLauncher\Tests\Extension\Utils;

use BehatLauncher\Extension\Utils\ClassDiscoverer;

class ClassDiscovererTest extends \PHPUnit_Framework_TestCase
{
    public function testDiscoverString_OneClass()
    {
        $discoverer = new ClassDiscoverer();
        $actual   = '<?php class foo {}';
        $expected = array('foo');
        $this->assertEquals($expected, $discoverer->discoverString($actual));
    }

    public function testDiscoverString_OneNamespace()
    {
        $discoverer = new ClassDiscoverer();
        $actual   = '<?php namespace foo; class bar {}';
        $expected = array('foo\\bar');
        $this->assertEquals($expected, $discoverer->discoverString($actual));
    }

    public function testDiscoverString_TwoClasses()
    {
        $discoverer = new ClassDiscoverer();
        $actual   = '<?php class foo {} class bar {}';
        $expected = array('foo', 'bar');
        $this->assertEquals($expected, $discoverer->discoverString($actual));
    }
}
