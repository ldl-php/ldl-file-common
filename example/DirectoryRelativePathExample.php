<?php

declare(strict_types=1);

/**
 * This example creates random files and directories with nested directories inside of them,
 * after they are created they will be deleted recursively.
 */

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/lib/example-helper.php';

use LDL\File\Directory;
use LDL\Framework\Base\Exception\InvalidArgumentException;

$dir = new Directory(__DIR__);

echo sprintf('Create relative path to directory %s%s', __DIR__, "\n\n");

echo sprintf('%s%s', $dir->mkpath('this', 'is', 'a', 'new', 'path'), "\n\n");

echo "Create relative path With an INCORRECT type in mkpath arguments, EXCEPTION must be thrown\n\n";

try {
    echo sprintf('%s%s', $dir->mkpath('this', 'is', 'a', [], 'new', 'path'), "\n\n");
} catch (InvalidArgumentException $e) {
    echo "OK Exception: {$e->getMessage()}\n";
}
