<?php declare(strict_types=1);

/**
 * This example creates random files and directories with nested directories inside of them,
 * after they are created they will be deleted recursively.
 */

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/lib/example-helper.php';

use LDL\File\Collection\FileCollection;

$tempDir = createTestFiles();

echo "Get test directory tree\n";
echo "####################################################\n";

$tree = $tempDir->getTree();

echo "Create a FileCollection by filtering directories in the tree:\n";
$files = $tree->filterFiles();

echo "Convert FileCollection to array:\n\n";

$files = $files->toArray();

echo "Create a FileCollection fromIterable:\n\n";

$fromIterable = FileCollection::fromIterable($files);

echo "Print files:\n";

foreach($fromIterable as $file){

    echo "File $file\n";

}

echo "Delete created directory recursively\n";
echo "####################################################\n";

$tempDir->delete();
