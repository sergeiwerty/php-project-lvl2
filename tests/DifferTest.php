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
        $plainData = file_get_contents(__DIR__ . "/fixtures/" . "result.txt");
        $this->expected = trim($plainData);
    }

    /**
     * @dataProvider equalityProvider
     */
    public function testFilesEquals($fileName1, $fileName2)
    {
//        $pathToFixture3 = getFixtureFullPath('plainYml1.yml');

        $this->assertEquals($this->expected, genDiff($fileName1, $fileName2));

    }

    public function equalityProvider()
    {
        return [
            ['plainJson1.json', 'plainJson2.json'],
            ['plainYml1.yml', 'plainYml2.yml']
        ];
    }
}
