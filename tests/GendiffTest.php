<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;

$autoloadPath1 = __DIR__ . '/../../../autoload.php';
$autoloadPath2 = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath1)) {
    require_once $autoloadPath1;
} else {
    require_once $autoloadPath2;
}

use function Gendiff\genDiff;

class GendiffTest extends TestCase
{
    public function testJSONEquals()
    {
        $diffResult = `
- follow: false
  host: hexlet.io
- proxy: 123.234.53.22
- timeout: 50
+ timeout: 20
+ verbose: true
        `;
    }
}