<?php declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use LDL\File\Validator\PathValidator;

$paths = [
    '/home/user/ldl-file-common',
    '/home/user/ldl-file-common/example',
    '/home/user/ldl-file-common/example/PathValidatorExample.php',
    '/home/user/ldl-file-common/src/File/Collection',
    '/home/user/ldl-file-common/src/file/Validator'
];

echo "Create PathValidator with path: '/home/user/ldl-file-common/example'\n";
$validator = new PathValidator('/home/user/ldl-file-common/example');

echo "Validate some paths\n";

foreach($paths as $path){
    echo "Validate: '$path'\n";

    try{
        $validator->validate($path);
        echo "OK!\n";
    }catch(\Exception $e){
        echo "EXCEPTION: {$e->getMessage()}\n";
    }

}

echo "\n\nCreate PathValidator with path: '/home/user/ldl-file-common/src/file'\n";
$validator = new PathValidator('/home/user/ldl-file-common/src/file');

echo "Validate some paths\n";
foreach($paths as $path){
    echo "Validate: '$path'\n";

    try{
        $validator->validate($path);
        echo "OK!\n";
    }catch(\Exception $e){
        echo "EXCEPTION: {$e->getMessage()}\n";
    }

}

echo "\n\nCreate NEGATED PathValidator with path: '/home/user/ldl-file-common/src/file'\n";
$validator = new PathValidator('/home/user/ldl-file-common/src/file', true);

echo "Validate some paths\n";
foreach($paths as $path){
    echo "Validate: '$path'\n";

    try{
        $validator->validate($path);
        echo "OK!\n";
    }catch(\Exception $e){
        echo "EXCEPTION: {$e->getMessage()}\n";
    }

}