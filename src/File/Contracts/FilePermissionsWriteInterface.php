<?php

declare(strict_types=1);

namespace LDL\File\Contracts;

use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileWriteException;

interface FilePermissionsWriteInterface
{
    /**
     * Sets permissions on a file and returns the very same instance of the file.
     *
     * @param int $permissions In octal mode
     *
     * @throws FileWriteException
     * @throws FileExistsException
     */
    public function chmod(int $permissions): LDLFileInterface;
}
