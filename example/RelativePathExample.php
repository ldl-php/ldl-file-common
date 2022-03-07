<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use LDL\File\Helper\FilePathHelper;
use LDL\Framework\Base\Exception\InvalidArgumentException;

echo "Create test files ...\n";

echo "Get relative path from /etc/cron.d to /tmp\n";

var_dump(FilePathHelper::getRelativePath('/etc/cron.d', '/tmp'));

try {
    echo "Try to create a relative path from a non-absolute path, EXCEPTION must be thrown\n";
    var_dump(FilePathHelper::getRelativePath('etc/cron.d/daily', '/etc/cron.d'));
} catch (InvalidArgumentException $e) {
    echo "OK: Exception {$e->getMessage()}\n";
}
