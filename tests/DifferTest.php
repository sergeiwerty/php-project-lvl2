<?php

namespace Differ\Tests;

use PHPUnit\Framework\TestCase;

//$autoloadPath1 = __DIR__ . '/../../../autoload.php';
//$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';
//
//if (file_exists($autoloadPath1)) {
//    require_once $autoloadPath1;
//} else {
//    require_once $autoloadPath2;
//}

use function Differ\Differ\genDiff;
use function Differ\Utils\getFixtureFullPath;

//chdir('../tests/fixtures');

class DifferTest extends TestCase
{
    protected $myString;

    protected function setUp(): void
    {
        $this->expected = "{
	- follow: false
	  host: 'hexlet.io'
	- proxy: '123.234.53.22'
	- timeout: 50
	+ timeout: 20
	+ verbose: true
}\n";
    }

    /**
     * @dataProvider equalityProvider
     */
    public function testFilesEquals($fileName1, $fileName2)
    {
//        $pathToFixture3 = getFixtureFullPath('filepath1.yml');

        $this->assertEquals($this->expected, genDiff($fileName1, $fileName2));

    }

    public function equalityProvider()
    {
        return [
            ['file1.json', 'file2.json']
//            ['filepath1.yml', 'filepath2.yml']
        ];
    }
}
