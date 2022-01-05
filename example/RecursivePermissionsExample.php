<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/lib/example-helper.php';

$tmpDir = createTestFiles();
echo "Chmod 0777 recursively ...\n\n";
$tmpDir->chmod(0777, true);
$tmpDir->delete();
