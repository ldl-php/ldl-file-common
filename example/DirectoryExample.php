<?php declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use LDL\File\Directory;
use LDL\File\Helper\PathHelper;

$tempDir = Directory::create(
    PathHelper::createAbsolutePath(sys_get_temp_dir(), 'ldl-file-common-test')
);

//$tempDir->getTree()->delete();

echo "Create some random directories in the system's temporary directory and append them to a Directory collection";

$directories = array_map(static function($directory) use ($tempDir){
    return $tempDir->mkdir((string)$directory);
}, range(1,100));

foreach($tempDir->getTree() as $file){
    echo sprintf('%s: %s%s', $file instanceof Directory ? 'Directory' : 'File', $file,"\n\n");
}
