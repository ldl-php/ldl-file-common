<?php declare(strict_types=1);

/**
 * In this example:
 *
 * 1) Generate random files and directories
 * 2) Obtain tree from temporary directory where files were generated
 * 3) Set random permissions (Readable and NON-Readable perms) on each file / directory
 * 4) Create a new ReadableFileCollection by filtering files in tree and then filtering for readable only files
 * 5) Print each readable file
 * 6) Fix all permissions in the tree so all files can be deleted correctly
 * 7) Delete testing directory
 */

use LDL\File\Collection\ReadableFileCollection;

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/lib/example-helper.php';

$tmpDir = createTestFiles();

$tree = $tmpDir->getTree();

/**
 * Assign readable and non readable permissions
 */
foreach($tree as $i => $file){
    $permission = ($i % 2) ? 0444 : 0000;
    $file->chmod($permission);

    echo sprintf('Set file %s as %s', $file, 0444 === $permission ? "readable\n" : "not readable\n");
}

$rfc = new ReadableFileCollection($tree->filterReadable()->filterFiles());

echo "\nPrint files which are readable: \n\n";

foreach($rfc as $readable){
    echo "$readable\n";
}

echo "\nAttempt to append entire file tree (which contains readable and non-readable files) EXCEPTION must be thrown\n\n";

try{
    $rfc->appendMany($tree->filterFiles());
}catch(\Exception $e){
    echo "OK EXCEPTION: {$e->getMessage()}";
}

/**
 * Fix all permissions so everything can be deleted
 */
$tree->chmod(0755);

deleteTestDir();