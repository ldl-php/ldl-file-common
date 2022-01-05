<?php

declare(strict_types=1);

namespace LDL\File\Contracts;

use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileReadException;
use LDL\File\Exception\FileTypeException;
use LDL\File\Exception\FileWriteException;

interface LinkInterface extends LDLFileInterface, FilePermissionsReadInterface
{
    /**
     * Returns the target to which this link points to.
     *
     * @return FileInterface|DirectoryInterface
     */
    public function getTarget();

    /**
     * @throws FileExistsException
     * @throws FileWriteException
     * @throws FileReadException
     * @throws FileTypeException
     */
    public static function create(
        string $source,
        string $dest,
        bool $force
    ): LinkInterface;
}
