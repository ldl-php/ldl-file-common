<?php

declare(strict_types=1);

namespace LDL\File\Helper;

use LDL\File\Constants\FileTypeConstants;
use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileReadException;
use LDL\File\Exception\FileWriteException;
use LDL\Framework\Base\Exception\InvalidArgumentException;

final class FileOwnershipHelper
{
    /**
     * @throws InvalidArgumentException
     * @throws FileExistsException
     * @throws FileReadException
     */
    public static function chown(string $file, ?string $user, ?string $group): void
    {
        if (null === $user && null === $user) {
            throw new InvalidArgumentException("User or group must be specified");
        }

        if (!is_readable($file)) {
            throw new FileReadException("Could not read file $file");
        }

        $throw = static function (string $file, string $type): void {
            throw new FileWriteException("Could not change $type ownership of file $file");
        };

        $type = FileHelper::getType($file);

        if (FileTypeConstants::FILE_TYPE_LINK === $type) {
            $user && !@lchown($file, $user) && $throw($file, 'user');
            $group && !@lchgrp($file, $group) && $throw($file, 'group');

            return;
        }

        $user && !@chown($file, $user) && $throw($file, 'user');
        $group && !@chgrp($file, $group) && $throw($file, 'group');
    }

    /**
     * @throws FileReadException
     */
    public static function getOwner(string $file): int
    {
        $owner = fileowner($file);

        if (false === $owner) {
            throw new FileReadException("Could not read file owner of file $file");
        }

        return $owner;
    }

    /**
     * @throws FileReadException
     */
    public static function getGroup(string $file): int
    {
        $group = filegroup($file);

        if (false === $group) {
            throw new FileReadException("Could not read file owner of file $file");
        }

        return $group;
    }
}
