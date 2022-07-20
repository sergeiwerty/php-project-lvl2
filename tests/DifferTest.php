<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class DifferTest extends TestCase
{
    public function getDataByFileName($filename): string
    {
        return trim(file_get_contents(__DIR__ . "/fixtures/" . $filename));
    }

    public $pathPrefix = 'tests/fixtures/data';

    /**
     * @dataProvider nestedFilesProvider
     */
    public function testGendiffWithStylishFormatter($format)
    {
        $expectedStylish = $this->getDataByFileName("expectedStylish.txt");
            $this->assertEquals(
                $expectedStylish,
                genDiff(
                    "{$this->pathPrefix}1.{$format}",
                    "{$this->pathPrefix}2.{$format}",
                    'stylish'
                )
            );
    }

    /**
     * @dataProvider nestedFilesProvider
     */
    public function testGendiffWithPlainFormatter($format)
    {
        $expectedPlain = $this->getDataByFileName("expectedPlain.txt");
            $this->assertEquals(
                $expectedPlain,
                genDiff(
                    "{$this->pathPrefix}1.{$format}",
                    "{$this->pathPrefix}2.{$format}",
                    'plain'
                )
            );
    }

    /**
     * @dataProvider nestedFilesProvider
     */
    public function testGendiffWithJsonFormatter($format)
    {
        $expectedJSON = $this->getDataByFileName("expectedJSON.txt");
            $this->assertEquals(
                $expectedJSON,
                genDiff(
                    "{$this->pathPrefix}1.{$format}",
                    "{$this->pathPrefix}2.{$format}",
                    'json'
                )
            );
    }

    /**
     * @dataProvider nestedFilesProvider
     */
    public function testGendiffWithDefaultFormatter($format)
    {
        $expectedStylish = $this->getDataByFileName("expectedStylish.txt");
            $this->assertEquals(
                $expectedStylish,
                genDiff(
                    "{$this->pathPrefix}1.{$format}",
                    "{$this->pathPrefix}2.{$format}",
                )
            );
    }

    public function nestedFilesProvider()
    {
        return [
            ['json'],
            ['yaml']
        ];
    }
}
