<?php

declare(strict_types=1);

namespace LDL\File\Contracts;

use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileTypeException;
use LDL\File\Exception\FileWriteException;

interface FileInterface extends LDLFileInterface, FilePermissionsReadInterface, ReadFileLinesInterface, LinkableInterface
{
    /**
     * Creates a new file.
     *
     * @throws FileExistsException
     * @throws FileTypeException
     * @throws FileWriteException
     */
    public static function create(
        string $file,
        string $contents,
        int $permissions,
        bool $overwrite
    ): FileInterface;

    /**
     * Returns file extension.
     */
    public function getExtension(): string;

    /**
     * @throws FileTypeException
     * @throws FileWriteException
     * @throws FileExistsException
     */
    public function put(string $contents, string $newName = null): FileInterface;

    public function append(string $content): FileInterface;

    /**
     * Returns file permissions in octal mode.
     *
     * @throws FileExistsException
     */
    public function getPerms(): int;

    public function isDeleted(): bool;

    public function isWritable(): bool;

    public function isReadable(): bool;
}
