<?php

namespace Alex\BehatLauncher\Tests\Behat;

use Alex\BehatLauncher\Behat\Run;

class RunTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateFeatures()
    {
        $run = new Run();
        $run->createUnits(array(
            'foo' => array(
                'bar' => true,
                'baz' => false,
                'foo' => array(
                    'foo' => false,
                    'bar' => false
                ),
                'foofoo' => array('bar' => true, 'baz' => true)
            )
        ));

        $this->assertCount(3, $run->getUnits());

        list($a, $b, $c) = $run->getUnits()->all();

        $this->assertEquals('foo/bar', $a->getFeature());
        $this->assertEquals('foo/foofoo/bar', $b->getFeature());
        $this->assertEquals('foo/foofoo/baz', $c->getFeature());
    }
}
