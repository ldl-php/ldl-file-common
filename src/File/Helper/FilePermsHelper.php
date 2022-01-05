<?php

declare(strict_types=1);

namespace LDL\File\Helper;

use LDL\File\Contracts\DirectoryInterface;
use LDL\File\Contracts\FileInterface;
use LDL\File\Contracts\LinkInterface;
use LDL\File\Directory;
use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileWriteException;

final class FilePermsHelper
{
    /**
     * @throws FileExistsException
     * @throws FileWriteException
     */
    public static function chmod(string $file, int $permissions, bool $recursive = false): void
    {
        if (!file_exists($file)) {
            throw new FileExistsException("File \"$file\" does not exists");
        }

        if (is_link($file)) {
            return;
        }

        $result = @chmod($file, $permissions);

        if (!$result) {
            throw new FileWriteException(sprintf('Failed to change file permissions to: %s', var_export($permissions, true)));
        }

        if (!$recursive || !is_dir($file)) {
            return;
        }

        $dir = new Directory($file);

        $tree = $dir->getTree();

        /**
         * @var DirectoryInterface|FileInterface $file
         */
        foreach ($tree as $file) {
            if ($file instanceof LinkInterface) {
                continue;
            }

            if (!$file instanceof DirectoryInterface) {
                $file->chmod($permissions);
                continue;
            }

            $file->chmod($permissions, true);
        }
    }

    /**
     * @throws FileExistsException
     */
    public static function getPerms(string $file): int
    {
        if (!file_exists($file)) {
            throw new FileExistsException("File $file does not exists!");
        }

        return fileperms($file);
    }
}
