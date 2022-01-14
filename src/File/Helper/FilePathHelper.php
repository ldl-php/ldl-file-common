<?php

declare(strict_types=1);

namespace LDL\File\Helper;

use LDL\Framework\Base\Exception\InvalidArgumentException;

final class FilePathHelper
{
    /**
     * Given a relative path, it will return it's absolute path, even if the path does not exists
     * If the flag $mustExist is set to true, realpath will be used (which checks the existence of the given $path)
     * if the path does not exists, an \InvalidArgumentException will be thrown.
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public static function getAbsolutePath(string $path, bool $mustExist = false): ?string
    {
        if ($mustExist) {
            $realPath = realpath($path);
            if (false === $realPath) {
                throw new \InvalidArgumentException("Given path: \"$path\" does not exist");
            }

            return $realPath;
        }

        $path = str_replace(['/', '\\'], \DIRECTORY_SEPARATOR, $path);

        $parts = array_filter(explode(\DIRECTORY_SEPARATOR, $path), static function ($p) {
            return '' !== $p;
        });

        $absolutes = [];

        foreach ($parts as $part) {
            if ('.' === $part) {
                continue;
            }

            '..' === $part ? array_pop($absolutes) : $absolutes[] = $part;
        }

        return sprintf('%s%s', \DIRECTORY_SEPARATOR, implode(\DIRECTORY_SEPARATOR, $absolutes));
    }

    /**
     * @param mixed ...$parts
     *
     * @throws InvalidArgumentException
     */
    public static function createAbsolutePath(...$parts): string
    {
        foreach ($parts as $num => $part) {
            if (is_array($part) || is_object($part) || null === $part) {
                throw new InvalidArgumentException(sprintf('Argument number "%s" is a compound type: "%s". Only string|int|double are accepted', $num, gettype($part)));
            }
        }

        return self::getAbsolutePath(implode(\DIRECTORY_SEPARATOR, $parts));
    }
}
