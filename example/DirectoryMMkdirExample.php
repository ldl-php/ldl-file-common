<?php

declare(strict_types=1);

/**
 * This example creates random files and directories with nested directories inside of them,
 * after they are created they will be deleted recursively.
 */

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/lib/example-helper.php';

use LDL\File\Directory;

$tempDir = createTestFiles();

echo "Create multiple directories inside test directory\n";
echo "####################################################\n";

$tempDir->mmkdir(range(500, 510));

echo "Get directory tree and print it out\n";
echo "####################################################\n";

$tree = $tempDir->getTree();

foreach ($tree as $file) {
    echo sprintf('%s: %s%s', $file instanceof Directory ? 'Directory' : 'File', $file, "\n");
}

echo "Delete created directory recursively\n";
echo "####################################################\n";

$tempDir->delete();
