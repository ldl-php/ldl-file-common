<?php

declare(strict_types=1);

namespace LDL\File\Contracts;

use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileWriteException;

interface FilePermissionsReadInterface
{
    /**
     * Gets file permissions.
     *
     * @throws FileExistsException
     */
    public function getPerms(): int;

    /**
     * Check if file is readable.
     */
    public function isReadable(): bool;

    /**
     * * Check if file is writable.
     */
    public function isWritable(): bool;
}
