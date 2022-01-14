<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/../example/lib/example-helper.php';

use LDL\File\Directory;
use LDL\File\FileTree;

echo sprintf('Create directory tree instance from directory %s%s', __DIR__, "\n\n");

$tree = new FileTree(new Directory(__DIR__));

echo sprintf('Get file %s from previously created tree%s', __FILE__, "\n\n");

$file = $tree->getFileByName(__FILE__);

echo "OK: Got file {$file}\n\n";
