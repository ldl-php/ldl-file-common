<?php

declare(strict_types=1);

namespace LDL\File\Helper;

use LDL\File\Contracts\FileTreeInterface;
use LDL\File\Directory;
use LDL\File\Exception\FileException;
use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileReadException;
use LDL\File\Exception\FileTypeException;
use LDL\File\Exception\FileWriteException;
use LDL\File\File;
use LDL\File\FileTree;
use LDL\Framework\Base\Exception\InvalidArgumentException;

final class DirectoryHelper
{
    /**
     * @throws FileExistsException
     * @throws FileReadException
     * @throws FileTypeException
     * @throws FileWriteException
     */
    public static function create(string $path, int $mode, bool $force = false): Directory
    {
        $exists = is_dir($path);

        if (!$force && $exists) {
            throw new FileExistsException("Directory \"$path\" already exists!");
        }

        if ($force && $exists) {
            self::delete($path);
        }

        if (!@mkdir($path, $mode, true) && !is_dir($path)) {
            throw new FileWriteException("Unable to create directory: $path");
        }

        return new Directory($path);
    }

    /**
     * Given a directory path, it will check if its empty or not.
     * If the directory path is not empty, delete all files including hidden files.
     * if the directory path is empty, delete the directory.
     *
     * @author Ohiare Nathaniel
     *
     * @throws FileTypeException
     * @throws FileReadException
     * @throws FileWriteException
     * @throws FileExistsException
     */
    public static function delete(string $dir): void
    {
        if (!is_dir($dir)) {
            throw new FileTypeException("Given path \"$dir\" is not a directory!");
        }

        if (!is_writable($dir)) {
            throw new FileWriteException("Directory \"$dir\" is not writable!");
        }

        $files = scandir($dir);

        foreach ($files as $file) {
            if ('.' === $file || '..' === $file) {
                continue;
            }

            $file = sprintf('%s%s%s', $dir, \DIRECTORY_SEPARATOR, $file);

            is_dir($file) ? self::delete($file) : FileHelper::delete($file);
        }

        rmdir($dir);
    }

    /**
     * @throws FileExistsException
     * @throws FileReadException
     * @throws FileTypeException
     * @throws FileWriteException
     */
    public static function copy(string $source, string $dest, bool $force = false): Directory
    {
        $perms = fileperms($source);

        if (!is_dir($source)) {
            throw new FileTypeException("Source $source is not a directory");
        }

        if (!is_readable($source)) {
            throw new FileReadException("Could not read source directory $source");
        }

        $exists = file_exists($dest);

        if (!$force && $exists) {
            throw new FileExistsException("Destination $dest already exists");
        }

        if ($force && $exists && is_dir($dest)) {
            self::delete($dest);
        }

        if ($force && $exists && is_file($dest)) {
            FileHelper::delete($dest);
        }

        if (!mkdir($dest, $perms, true) && !is_dir($dest)) {
            throw new FileWriteException(sprintf('Could not create directory "%s"', $dest));
        }

        $files = scandir($source);

        foreach ($files as $file) {
            if ('.' === $file || '..' === $file) {
                continue;
            }

            $fileSource = sprintf('%s%s%s', $source, \DIRECTORY_SEPARATOR, $file);
            $fileDest = sprintf('%s%s%s', $dest, \DIRECTORY_SEPARATOR, $file);

            try {
                is_dir($fileSource) ? self::copy($fileSource, $fileDest) : FileHelper::copy($fileSource, $fileDest);
            } catch (FileException $e) {
            }
        }

        return new Directory($dest);
    }

    /**
     * @throws FileExistsException
     * @throws FileReadException
     * @throws FileTypeException
     * @throws FileWriteException
     */
    public static function move(string $source, string $dest, bool $force = false): Directory
    {
        $dir = self::copy($source, $dest, $force);

        try {
            self::delete($source);
        } catch (FileException $e) {
            self::delete($dest);
            throw new FileWriteException("Could not remove source directory: $source", $e->getCode(), $e);
        }

        return $dir;
    }

    /**
     * Obtains a FileTree.
     *
     * @throws FileReadException
     * @throws FileTypeException
     */
    public static function getTree(string $path): FileTreeInterface
    {
        $dir = new Directory($path);

        if (!$dir->isReadable()) {
            throw new FileReadException("Directory \"$path\" is not readable!");
        }

        return new FileTree($dir, self::getFilesAsStringArray($path));
    }

    /**
     * @return string[]
     *
     * @throws FileReadException
     * @throws FileTypeException
     */
    public static function getFilesAsStringArray(string $path, bool $recursive = false): array
    {
        $dir = new Directory($path);

        if (!$dir->isReadable()) {
            throw new FileReadException("Directory \"$path\" is not readable!");
        }

        $files = [];

        foreach (scandir($path) as $file) {
            if ('.' === $file || '..' === $file) {
                continue;
            }

            $filePath = FilePathHelper::createAbsolutePath($dir->toString(), $file);

            if (!$recursive) {
                $files[] = $filePath;
                continue;
            }

            $files += self::getFilesAsStringArray($path, $recursive);
        }

        return $files;
    }

    /**
     * @throws FileExistsException
     * @throws FileReadException
     * @throws FileTypeException
     */
    public static function iterateTree(string $path): iterable
    {
        $dir = new Directory($path);

        if (!$dir->isReadable()) {
            throw new FileReadException("Directory $dir is not readable!");
        }

        foreach (scandir($path) as $file) {
            if ('.' === $file || '..' === $file) {
                continue;
            }
            $file = FilePathHelper::createAbsolutePath($dir->toString(), $file);
            yield is_dir($file) ? new Directory($file) : new File($file);
        }
    }

    /**
     * @throws FileExistsException
     * @throws FileReadException
     * @throws FileTypeException
     * @throws InvalidArgumentException
     */
    public static function chown(
        string $path,
        ?string $user,
        ?string $group,
        bool $recursive = false
    ): void {
        $dir = new Directory($path);

        if (!$dir->isReadable()) {
            throw new FileReadException("Directory $dir is not readable!");
        }

        FileOwnershipHelper::chown($dir->getPath(), $user, $group);

        if (!$recursive) {
            return;
        }

        foreach ($dir->getTree()->traverse() as $file) {
            FileOwnershipHelper::chown((string) $file, $user, $group);
        }
    }
}
