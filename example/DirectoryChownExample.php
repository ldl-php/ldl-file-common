<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/lib/example-helper.php';

echo "Create test files ...\n";
$dir = createTestFiles();

echo "Chown recursively the created directory to user 'ldl'\n\n";
$dir->chown('ldl', 'ldl', true);
$dir->delete();
