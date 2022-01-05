<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/lib/example-helper.php';

use LDL\File\Helper\FilePathHelper;

echo "Create test directory ...\n";
$dir = createTestFiles();

echo "Created directory $dir\n\n";

$destination = FilePathHelper::createAbsolutePath(sys_get_temp_dir(), sprintf('ldl_%s', md5(__FILE__)));

echo "Copy directory $dir to $destination ...\n";

$copy = $dir->copy($destination, true);
