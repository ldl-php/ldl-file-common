<?php

declare(strict_types=1);

namespace LDL\File\Helper;

use LDL\File\Contracts\LinkInterface;
use LDL\File\Directory;
use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileReadException;
use LDL\File\Exception\FileTypeException;
use LDL\File\Exception\FileWriteException;
use LDL\File\File;
use LDL\File\Link;

final class LinkHelper
{
    /**
     * @throws FileExistsException
     * @throws FileWriteException
     * @throws FileReadException
     * @throws FileTypeException
     */
    public static function create(string $source, string $target, bool $force = false): LinkInterface
    {
        $exists = file_exists($target);
        $isLink = is_link($target);

        if (!$exists) {
            if (!@symlink($source, $target)) {
                throw new FileWriteException("Unable to create symlink from: $source to: $target");
            }

            return new Link($target);
        }

        if ($isLink) {
            return new Link($target);
        }

        if (!$force) {
            throw new FileExistsException("Link $target already exists");
        }

        is_dir($target) ? DirectoryHelper::delete($target) : FileHelper::delete($target);

        if (!@symlink($source, $target)) {
            throw new FileWriteException("Unable to create symlink to $target");
        }

        return new Link($target);
    }

    /**
     * @throws FileExistsException
     * @throws FileReadException
     * @throws FileTypeException
     *
     * @return Directory|File
     */
    public static function getTarget(string $file)
    {
        if (!file_exists($file)) {
            throw new FileExistsException("File $file does not exists");
        }

        if (!is_readable($file)) {
            throw new FileReadException("File $file is not readable");
        }

        if (!is_link($file)) {
            throw new FileTypeException("File $file is not a symlink");
        }

        $target = readlink($file);

        return is_dir($target) ? new Directory($target) : new File($target);
    }
}
