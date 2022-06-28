<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;
use function Differ\Utils\getFixtureFullPath;
use function Differ\Differ\Builder\buildDiff;

//chdir('../tests/fixtures');

class DifferTest extends TestCase
{
    protected $myString;

    protected function setUp(): void
    {
        $plainData = file_get_contents(__DIR__ . "/fixtures/" . "result.txt");
        $nestedData = file_get_contents(__DIR__ . "/fixtures/" . "resultNested.txt");
        $this->expected = trim($plainData);
        $this->expectedNested = trim($nestedData);
    }

    /**
     * @dataProvider equalityPlainProvider
     */
    public function testFilesEquals($fileName1, $fileName2)
    {
        $this->assertEquals($this->expected, genDiff($fileName1, $fileName2));
    }

    public function equalityPlainProvider()
    {
        return [
            ['plainJson1.json', 'plainJson2.json'],
            ['plainYml1.yml', 'plainYml2.yml'],
        ];
    }


    /**
     * @dataProvider equalityNestedProvider
     */
    public function testNestedEquals($fileName1, $fileName2)
    {
        $this->assertEquals($this->expectedNested, genDiff($fileName1, $fileName2));
    }

    public function equalityNestedProvider()
    {
        return [
            ['nestedJson1.json', 'nestedJson2.json'],
            ['nestedYaml1.yaml', 'nestedYaml2.yaml']
        ];
    }

    public function testBuilder()
    {
        $this->assertEquals($this->expectedNested, buildDiff('nestedJson1.json', 'nestedJson2.json'));
    }
}
