<?php declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use LDL\File\Helper\PathHelper;

function testPath(string $relative, string $absolute, bool $mustExist=false) : void
{
    echo "Resolve relative path: \"$relative\", resolved path must be equal to: $absolute\n";

    if($mustExist){
        echo "Given path MUST exist\n";
    }

    $resolved = PathHelper::getAbsolutePath($relative, $mustExist);

    if ($resolved !== $absolute) {
        throw new \RuntimeException("Failed to assert $resolved === $absolute");
    }

    echo "OK\n";
}


$paths = [
    [
        'relative' => '/home/../etc/passwd',
        'absolute' => '/etc/passwd',
        'mustExist' => false
    ],
    [
        'relative' => '../../home/test',
        'absolute' => '/home/test',
        'mustExist' => false
    ],
    [
        'relative' => sys_get_temp_dir().'/../'.sys_get_temp_dir(),
        'absolute' => sys_get_temp_dir(),
        'mustExist' => true
    ]
];

foreach($paths as $test){
    testPath($test['relative'], $test['absolute'], $test['mustExist']);
}

echo "\nTest exception with a non existent path\n";
echo "#############################################################\n\n";


$thrown = false;

try {
    testPath('/non/../existent/path', '/non/existent/path', true);
}catch(\InvalidArgumentException $e){
    $thrown = true;
    echo $e->getMessage()."\n";
}

if(!$thrown){
    throw new \Exception('non existent path check did not throw an exception!');
}

echo "\nTest createAbsolutePath\n";
echo "#########################################\n\n";

$result = PathHelper::createAbsolutePath('home','ldl','ldl-project');
$expected = '/home/ldl/ldl-project';

echo "Result is: $result\n";

if($result !== $expected){
    throw new \Exception("Result $result is not equal to expected $expected");
}

echo "OK\n";
