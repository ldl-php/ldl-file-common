<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/lib/example-helper.php';

use LDL\File\Constants\FileTypeConstants;

echo "Create test files ...\n";

echo createTestFiles()->getTree()
    ->filterByFileType(FileTypeConstants::FILE_TYPE_REGULAR)
    ->get(0);
