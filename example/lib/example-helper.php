<?php declare(strict_types=1);

/**
 * Most examples require testing with real files, we create those test files here.
 */

use LDL\File\Helper\FilePathHelper;
use LDL\File\File;
use LDL\Framework\Helper\IterableHelper;
use LDL\File\Directory;
use LDL\File\Exception\FileExistsException;

define('FILE_COMMON_TEST_DIR', FilePathHelper::createAbsolutePath(sys_get_temp_dir(),'ldl-file-common'));

function createTestFiles() : Directory
{
    try{
        $tempDir = Directory::create(FILE_COMMON_TEST_DIR);
    }catch(FileExistsException $e){
        deleteTestDir();
        $tempDir = Directory::create(FILE_COMMON_TEST_DIR);
    }

    echo "Create some random files in $tempDir and append them to a Directory collection\n\n";

    IterableHelper::map(range(1,10), static function($file, $i) use ($tempDir){
            /**
         * Randomize whether to create a file or a directory
         */
        $file = ($i % 2) > 0 ? $tempDir->mkdir((string) $file) : File::create("{$tempDir}/{$file}.txt", uniqid('', true),null,null);

        /**
         * If a directory was created, create a nested directory, we do this to test recursive deletion
         */
        if($file instanceof Directory){
            $file->mkdir((string)random_int(100,200));
        }
    });

    IterableHelper::map(range(11,15), static function($file) use ($tempDir){

        $tempDir->mkfile(".hidden_$file");

    });

    return $tempDir;
}

function deleteTestDir() : void
{
    echo "\nClean up generated files and directories ... \n";
    $tempDir = new Directory(FILE_COMMON_TEST_DIR);
    $tempDir->delete();
}
