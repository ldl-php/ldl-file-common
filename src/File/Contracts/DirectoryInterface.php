<?php

declare(strict_types=1);

namespace LDL\File\Contracts;

use LDL\File\Collection\Contracts\DirectoryCollectionInterface;
use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileReadException;
use LDL\File\Exception\FileTypeException;
use LDL\File\Exception\FileWriteException;
use LDL\Framework\Base\Exception\InvalidArgumentException;

interface DirectoryInterface extends LDLFileInterface, FilePermissionsReadInterface, FilePermissionsWriteInterface, LinkableInterface
{
    /**
     * @throws FileExistsException
     * @throws FileWriteException
     */
    public static function create(string $path, int $permissions, bool $overwrite): DirectoryInterface;

    /**
     * Creates a directory inside of the current directory.
     *
     * @throws FileExistsException
     * @throws FileWriteException
     * @throws FileTypeException
     */
    public function mkdir(string $path, int $permissions, bool $force): DirectoryInterface;

    /**
     * Creates many directories inside the current directory, returns created directories as a DirectoryCollection.
     */
    public function mmkdir(
        iterable $directories,
        int $permissions = 0755,
        bool $overwrite = false
    ): DirectoryCollectionInterface;

    /**
     * Creates a new file in the current directory.
     *
     * @throws FileExistsException
     * @throws FileTypeException
     * @throws FileWriteException
     */
    public function mkfile(
        string $name,
        string $contents,
        int $permissions,
        bool $overwrite
    ): FileInterface;

    /**
     * Returns a Tree (containing FileInterface objects and DirectoryInterface objects).
     *
     * @throws FileReadException
     * @throws FileTypeException
     * @throws FileExistsException
     */
    public function getTree(): FileTreeInterface;

    /**
     * Returns a string path relative to the current directory.
     *
     * This will NOT create any file or directory.
     *
     * @param iterable ...$pieces
     */
    public function mkpath(...$pieces): string;

    /**
     * Obtains the relative path from the directory's path to another path, the path must be absolute.
     *
     * NOTE: The $to path does not need to exist
     *
     * @throws InvalidArgumentException
     */
    public function getRelativePath(string $to): string;

    /**
     * Returns the real directory path. This makes most sense when the Directory
     * instance was created from a relative path (such as ./directory_name).
     *
     * @throws FileExistsException (In case the directory has been moved or deleted)
     */
    public function getRealPath(): string;
}
