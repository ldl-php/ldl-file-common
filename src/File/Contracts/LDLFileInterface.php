<?php

declare(strict_types=1);

namespace LDL\File\Contracts;

use LDL\File\Constants\FileTypeConstants;
use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileReadException;
use LDL\File\Exception\FileTypeException;
use LDL\File\Exception\FileWriteException;
use LDL\Framework\Base\Contracts\Type\ToStringInterface;

interface LDLFileInterface extends ToStringInterface
{
    /**
     * Returns file type.
     *
     * @see FileTypeConstants
     *
     * @throws FileExistsException
     * @throws FileReadException
     */
    public function getType(): string;

    /**
     * Obtains the directory where this file is located.
     *
     * @throws FileTypeException
     * @throws FileExistsException
     */
    public function getDirectory(int $levels = null): DirectoryInterface;

    public function getPath(): string;

    /**
     * @throws FileExistsException
     * @throws FileReadException
     * @throws FileTypeException
     * @throws FileWriteException
     */
    public function move(string $dest, bool $overwrite): LDLFileInterface;

    /**
     * Deletes a file, if the file is a directory the directory must be recursively deleted.
     *
     * @throws FileExistsException
     * @throws FileTypeException
     * @throws FileReadException
     * @throws FileWriteException
     */
    public function delete(): void;

    /**
     * @throws FileExistsException
     * @throws FileReadException
     * @throws FileTypeException
     * @throws FileWriteException
     */
    public function copy(string $dest, bool $overwrite): LDLFileInterface;

    /**
     * Check if the targeted file in the object instance has been deleted.
     */
    public function isDeleted(): bool;
}
