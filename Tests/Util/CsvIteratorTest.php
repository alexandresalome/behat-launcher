<?php

namespace Alex\BehatLauncherBundle\Tests\Util;

use Alex\BehatLauncherBundle\Util\CsvIterator;

class CsvIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testNoFile()
    {
        $file = sys_get_temp_dir().'/bl_no_csv';
        $index = new CsvIterator($file);

        $this->assertFalse(file_exists($file));
        $this->assertEquals(array(), $index->search());
        $this->assertFalse(file_exists($file));
    }

    public function testSearch()
    {
        $file = sys_get_temp_dir().'/bl_csv';
        $content = <<<CSV
foo,A,B
foo,A,C
foo,A,D
bar,X,X
bar,X,Y
bar,X,Z
CSV
        ;
        file_put_contents($file, $content);

        $index = new CsvIterator($file);

        $this->assertEquals(array(
            array('foo', 'A', 'D'),
            array('foo', 'A', 'C'),
            array('foo', 'A', 'B')
        ), $index->search(array(0 => 'foo')));
    }

    public function testAddRow()
    {
        $file = sys_get_temp_dir().'/bl_csv';
        file_put_contents($file, '');
        $index = new CsvIterator($file);
        $this->assertEquals(array(), $index->search());

        $index->addRow(array('bar', 'A', 'B'));
        $index->addRow(array('foo', 'A', 'C'));
        $index->addRow(array('bar', 'A', 'D'));

        $this->assertEquals(array(
            array('bar', 'A', 'D'),
            array('bar', 'A', 'B'),
        ), $index->search(array(0 => 'bar')));
    }
}
