<?php declare(strict_types=1);

/**
 * This example creates random files and directories with nested directories inside of them,
 * after they are created they will be deleted recursively.
 */

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/lib/example-helper.php';

use LDL\File\File;

echo "Create a new File instance, first argument is this very same script file:\n\n";
$file = new File(__FILE__);

echo "Print lines from the file as a string:\n\n";
echo $file->getLinesAsString();

echo "Call File::getLines, must return a StringCollection\n\n";
$lines = $file->getLines();

echo "Print amount of lines:\n\n";
echo count($lines)."\n";

echo "Implode lines using '>> ' as separator:\n\n";
echo $lines->implode('>> ');

echo "\n\nImplode lines using '' as separator:\n\n";
echo $lines->implode('');

