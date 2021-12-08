<?php declare(strict_types=1);

/**
 * This example creates random files and directories with nested directories inside of them,
 * after they are created they will be deleted recursively.
 */

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/lib/example-helper.php';

use LDL\File\Collection\DirectoryCollection;

$tempDir = createTestFiles();

echo "Get test directory tree\n";
echo "####################################################\n";

$tree = $tempDir->getTree();

echo "Create a DirectoryCollection by filtering directories in the tree:\n";
$directories = $tree->filterDirectories();

echo "Convert DirectoryCollection to array:\n\n";

$directories = $directories->toArray();

echo "Create a DirectoryCollection fromIterable:\n\n";

$fromIterable = DirectoryCollection::fromIterable($directories);

echo "Print directories:\n";

foreach($fromIterable as $directory){

    echo "Directory $directory\n";

}

echo "Delete created directory recursively\n";
echo "####################################################\n";

$tempDir->delete();
