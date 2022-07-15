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
    public function testGendiffWithStylishFormatter()
    {
        $expectedStylish = $this->getDataByFileName("expectedStylish.txt");
        array_map(function ($format) use ($expectedStylish) {
            $this->assertEquals(
                $expectedStylish,
                genDiff(
                    "{$this->pathPrefix}1.{$format}",
                    "{$this->pathPrefix}2.{$format}",
                    'stylish'
                )
            );
        },
            func_get_args());
    }

    /**
     * @dataProvider nestedFilesProvider
     */
    public function testGendiffWithPlainFormatter()
    {
        $expectedPlain = $this->getDataByFileName("expectedPlain.txt");
        array_map(function ($format) use ($expectedPlain) {
            $this->assertEquals(
                $expectedPlain,
                genDiff(
                    "{$this->pathPrefix}1.{$format}",
                    "{$this->pathPrefix}2.{$format}",
                    'plain'
                )
            );
        },
            func_get_args());
    }

    /**
     * @dataProvider nestedFilesProvider
     */
    public function testGendiffWithJsonFormatter()
    {
        $expectedJSON = $this->getDataByFileName("expectedJSON.txt");
        array_map(function ($format) use ($expectedJSON) {
            $this->assertEquals(
                $expectedJSON,
                genDiff(
                    "{$this->pathPrefix}1.{$format}",
                    "{$this->pathPrefix}2.{$format}",
                    'json'
                )
            );
        },
            func_get_args());
    }

    /**
     * @dataProvider nestedFilesProvider
     */
    public function testGendiffWithDefaultFormatter()
    {
        $expectedStylish = $this->getDataByFileName("expectedStylish.txt");
        array_map(function ($format) use ($expectedStylish) {
            $this->assertEquals(
                $expectedStylish,
                genDiff(
                    "{$this->pathPrefix}1.{$format}",
                    "{$this->pathPrefix}2.{$format}",
                )
            );
        },
            func_get_args());
    }

    public function nestedFilesProvider()
    {
        return [
            ['json', 'yaml']
        ];
    }
}
