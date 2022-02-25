<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/lib/example-helper.php';

use LDL\File\File;

echo "Create a new File instance, first argument is this very same script file:\n";
echo "#########################################################################\n\n";
$file = new File(__FILE__);

$tempDir = sys_get_temp_dir();

echo "Copy the file to $tempDir\n";
echo "#########################################################################\n\n";

$newFile = $file->copy($tempDir, true);

echo "\nPrint file copy location\n";
echo "#########################################################################\n\n";

echo $newFile->toString();

echo "\n\nDelete file copy\n";
echo "#########################################################################\n\n";

$newFile->delete();

echo "DONE\n\n";
