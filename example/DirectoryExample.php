<?php declare(strict_types=1);

/**
 * This example creates random files and directories with nested directories inside of them,
 * after they are created they will be deleted recursively.
 */

require __DIR__.'/../vendor/autoload.php';

use LDL\File\Directory;
use LDL\File\Helper\DirectoryHelper;
use LDL\File\Helper\PathHelper;
use LDL\File\File;

$path = PathHelper::createAbsolutePath(sys_get_temp_dir(), 'ldl-file-common-test');

/**
 * In case execution was aborted and the script failed to delete the directory
 */
if(is_dir($path)){
    DirectoryHelper::delete($path);
}

$tempDir = Directory::create($path);

echo "Create some random files and directories in the system's temp dir and append them to a Directory collection";

$directories = array_map(static function($file) use ($tempDir){
    /**
     * Randomize whether to create a file or a directory
     */
    $file = random_int(0,1) === 1 ? $tempDir->mkdir((string) $file) : File::create("{$tempDir}/{$file}.txt", uniqid('', true));

    /**
     * If a directory was created, create a nested directory, we do this to test recursive deletion
     */
    if($file instanceof Directory){
        $file->mkdir((string)random_int(100,200));
    }
}, range(1,20));

echo "Get directory tree and print it out\n";
echo "####################################################\n";

$tree = $tempDir->getTree();

foreach($tree as $file){
    echo sprintf('%s: %s%s', $file instanceof Directory ? 'Directory' : 'File', $file,"\n");
}

echo "Delete created directory recursively\n";
echo "####################################################\n";

$tempDir->delete();
