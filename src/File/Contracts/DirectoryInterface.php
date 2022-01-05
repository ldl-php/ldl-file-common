<?php

declare(strict_types=1);

namespace LDL\File\Contracts;

use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileReadException;
use LDL\File\Exception\FileTypeException;
use LDL\File\Exception\FileWriteException;

interface DirectoryInterface extends LDLFileInterface, FilePermissionsReadInterface, FilePermissionsWriteInterface, LinkableInterface
{
    /**
     * @throws FileExistsException
     * @throws FileWriteException
     */
    public static function create(string $path, int $permissions): DirectoryInterface;

    /**
     * Returns a Tree (containing FileInterface objects and DirectoryInterface objects).
     *
     * @throws FileReadException
     * @throws FileTypeException
     * @throws FileExistsException
     */
    public function getTree(): FileTreeInterface;

    /**
     * Creates a directory inside of the current directory.
     *
     * @throws FileExistsException
     * @throws FileWriteException
     * @throws FileTypeException
     */
    public function mkdir(string $path, int $permissions = 0755): DirectoryInterface;

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
        int $permissions
    ): FileInterface;
}
