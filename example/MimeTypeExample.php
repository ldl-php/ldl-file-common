<?php declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use LDL\File\File;
use LDL\File\Helper\FileHelper;
use LDL\File\Exception\FileException;

echo "Create a new File instance\n\n";
$file = new File(__FILE__);

echo "Get file mime type from File->getMimeType():\n\n";
echo $file->getMimeType() . "\n\n";

echo "Get file mime type from MimeHelper::getType():\n\n";
echo FileHelper::getMimeType(__FILE__) . "\n\n";

try {
    echo "throws exception if mime database doesn't exists\n\n";
    FileHelper::getMimeType(__FILE__, '/usr/share/misc/magic.mime');
} catch (FileException $e) {
    echo "EXCEPTION: {$e->getMessage()}\n";
}
