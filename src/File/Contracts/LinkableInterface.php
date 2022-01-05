<?php

declare(strict_types=1);

namespace LDL\File\Contracts;

use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileReadException;
use LDL\File\Exception\FileTypeException;
use LDL\File\Exception\FileWriteException;

interface LinkableInterface
{
    /**
     * @throws FileExistsException
     * @throws FileWriteException
     * @throws FileReadException
     * @throws FileTypeException
     */
    public function link(string $dest, bool $force): LinkInterface;
}
