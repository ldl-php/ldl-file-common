<?php

declare(strict_types=1);

/**
 * Most examples require testing with real files, we create those test files here.
 */

use LDL\File\Constants\FileTypeConstants;
use LDL\File\Contracts\DirectoryInterface;
use LDL\File\Directory;
use LDL\File\Exception\FileExistsException;
use LDL\File\File;
use LDL\File\Helper\DirectoryHelper;
use LDL\File\Helper\FilePathHelper;
use LDL\Framework\Helper\IterableHelper;

define('FILE_COMMON_TEST_DIR', FilePathHelper::createAbsolutePath(sys_get_temp_dir(), 'ldl-file-common'));

function createTestFiles(): Directory
{
    try {
        $tempDir = Directory::create(FILE_COMMON_TEST_DIR);
    } catch (FileExistsException $e) {
        DirectoryHelper::delete(FILE_COMMON_TEST_DIR);
        $tempDir = Directory::create(FILE_COMMON_TEST_DIR);
    }

    echo "Create some random files in $tempDir and append them to a Directory collection\n\n";

    IterableHelper::map(range(1, 10), static function ($file, $i) use ($tempDir) {
        /**
         * Randomize whether to create a file or a directory.
         */
        $file = ($i % 2) > 0 ? $tempDir->mkdir((string) $file) : File::create("{$tempDir}/{$file}.txt", uniqid('', true));

        $file->link(
            FilePathHelper::createAbsolutePath(
                $tempDir->getPath(),
                md5($file->getPath())
            ),
            true
        );

        /*
         * If a directory was created, create a nested directory, we do this to test recursive deletion
         */
        if ($file instanceof DirectoryInterface) {
            $file->mkdir((string) ($i + 100));
        }
    });

    IterableHelper::map(range(11, 15), static function ($file) use ($tempDir) {
        $tempDir->mkfile(".hidden_$file");
    });

    return $tempDir;
}

function randomizePermissions(DirectoryInterface $dir): void
{
    /*
 * Assign readable and non readable permissions to files which are NOT links
 */
    $i = 0;
    foreach ($dir->getTree()->filterByFileType(FileTypeConstants::FILE_TYPE_LINK, true) as $i => $file) {
        $permission = $i ? 0444 : 0000;
        $i = 0 === $i ? 1 : 0;
        $file->chmod($permission);

        echo sprintf('Set file %s as %s', $file, 0444 == $permission ? "readable\n" : "not readable\n");
    }
}
