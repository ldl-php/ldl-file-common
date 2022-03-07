<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use LDL\File\Helper\FilePathHelper;
use LDL\Framework\Base\Exception\InvalidArgumentException;

echo "Create test files ...\n";

$pathA = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '/etc/cron.d';
$pathB = isset($_SERVER['argv'][2]) ? $_SERVER['argv'][2] : '/etc/cron.d/temp';

echo "Get relative path from $pathA to $pathB\n";

var_dump(FilePathHelper::getRelativePath($pathA, $pathB));

try {
    echo "Try to create a relative path from a non-absolute path, EXCEPTION must be thrown\n";
    var_dump(FilePathHelper::getRelativePath('etc/cron.d/daily', '/etc/cron.d'));
} catch (InvalidArgumentException $e) {
    echo "OK: Exception {$e->getMessage()}\n";
}
