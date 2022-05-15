<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;

$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';

use function Differ\Differ\generateDiff;

if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} else {
    require_once $autoloadPath2;
}

class GendiffTest extends TestCase
{
    public function testJSONEquals()
    {
        getcwd();
        chdir('./fixtures');
        $dir = getcwd();
        $dirFile = $dir . '/fixture1.json';
        $dirContent = file_get_contents($dirFile);
        $json = json_decode($dirContent, true);

//        $correctAnswer = [];
//        foreach ($json as $key => $value) {
//            $correctAnswer[$key] = $value;
//        }

        $this->assertEquals($json, []);
    }
}
