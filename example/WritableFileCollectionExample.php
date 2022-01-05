<?php

declare(strict_types=1);

/**
 * In this example:.
 *
 * 1) Generate random files and directories
 * 2) Obtain tree from temporary directory where files were generated
 * 3) Set random permissions (Writable and NON-Writable perms) on each file / directory
 * 4) Create a new WritableFileCollection by filtering files in tree and then filtering for writable only files
 * 5) Print each writable file
 * 6) Fix all permissions in the tree so all files can be deleted correctly
 * 7) Delete testing directory
 */

use LDL\File\Collection\WritableFileCollection;

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/lib/example-helper.php';

$tmpDir = createTestFiles();
randomizePermissions($tmpDir);

$tree = $tmpDir->getTree();

$rfc = new WritableFileCollection($tree->filterWritable()->filterFiles());

echo "\nPrint files which are writable: \n\n";

foreach ($rfc as $writable) {
    echo "$writable\n";
}

echo "\nAttempt to append entire file tree (which contains writable and non-writable files) EXCEPTION must be thrown\n\n";

try {
    $rfc->appendMany($tree->filterFiles());
} catch (\Exception $e) {
    echo "OK EXCEPTION: {$e->getMessage()}";
}

/*
 * Fix all permissions so everything can be deleted
 */
$tmpDir->chmod(0755, true);
$tmpDir->delete();
