<?php

declare(strict_types=1);

namespace LDL\File\Factory;

use LDL\File\Constants\FileTypeConstants;
use LDL\File\Contracts\FileTreeInterface;
use LDL\File\Contracts\LDLFileInterface;
use LDL\File\Directory;
use LDL\File\File;
use LDL\File\Link;

final class FileFactory
{
    public static function create(string $file): LDLFileInterface
    {
        if (is_link($file)) {
            return new Link($file);
        }

        if (is_dir($file)) {
            return new Directory($file);
        }

        return new File($file);
    }

    public static function fromTree(string $file, FileTreeInterface $tree)
    {
        $file = self::create($file);

        switch ($file->getType()) {
            case FileTypeConstants::FILE_TYPE_DIRECTORY:
                return new Directory($file->getPath(), $tree);
            break;

            case FileTypeConstants::FILE_TYPE_LINK:
                return new Link($file->getPath(), $tree);
            break;

            default:
                return new File($file->getPath(), $tree);
            break;
        }
    }
}
