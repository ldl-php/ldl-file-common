<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use LDL\File\Directory;
use LDL\Framework\Helper\IterableHelper;

$dir = new Directory(__DIR__);

$paths = [];
for ($i = 0; $i < 10; $i++) {
    $paths[] = $i;
}

$paths = $dir->mkpaths($paths);

IterableHelper::map($paths, static function ($p): void {
    echo $p."\n";
});
