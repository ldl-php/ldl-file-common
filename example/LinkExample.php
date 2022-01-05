<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/lib/example-helper.php';

use LDL\File\File;
use LDL\File\Helper\FilePathHelper;

echo "Create a new File instance, first argument is this very same script file:\n\n";
$file = new File(__FILE__);

$tmpLink = FilePathHelper::createAbsolutePath(
    sys_get_temp_dir(),
    sprintf('ldl_%s', md5_file(__FILE__))
);

echo "Symlink the created file to $tmpLink ...\n";
$link = $file->link($tmpLink);

echo $link->getPath()."\n";

echo "Get link target ...\n";
echo "{$link->getTarget()}\n";

echo "Copy link ...\n";

$l = $link->copy($tmpLink, true);
echo $l->getPath()."\n";

$moveTo = FilePathHelper::createAbsolutePath(
    sys_get_temp_dir(),
    sprintf('ldl_%s_2', md5_file(__FILE__))
);

echo "Move link {$l->getPath()} to $moveTo ...\n";
$moved = $l->move($moveTo);

echo "Remove link ...\n";

$moved->delete();
