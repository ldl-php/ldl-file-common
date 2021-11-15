<?php declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use LDL\File\Helper\DirectoryHelper;

function testPath(string $dir)
{  
    $resolved = DirectoryHelper::deleteDirectory($dir);
}


$paths = [
    [
        'relative' => 'love'
    ],
];

foreach($paths as $test){
    testPath($test['relative']);
}

echo "OK\n";
