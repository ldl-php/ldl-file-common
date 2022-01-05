<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/lib/example-helper.php';

use LDL\File\Helper\FilePathHelper;

echo "Create test directory ...\n";
$dir = createTestFiles();

$dir->chmod(0755);

echo "Created directory $dir\n\n";

$destination = FilePathHelper::createAbsolutePath(sys_get_temp_dir(), sprintf('ldl_%s', md5(__FILE__)));

echo "Move directory $dir to $destination ...\n";

$moved = $dir->move($destination, true);
