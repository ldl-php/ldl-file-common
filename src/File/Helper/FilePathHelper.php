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

    /**
     * Normalize path separator to $separator ('/' by default).
     */
    public static function normalize(string $path, string $separator = '/'): string
    {
        return preg_replace(sprintf('#%s#', \DIRECTORY_SEPARATOR), $separator, $path);
    }

    /**
     * Obtains the relative path from a path $from to another path $to.
     *
     * Both paths passed in $from and $to must be absolute
     *
     * NOTE: Path's do not need to exist.
     */
    public static function getRelativePath(string $from, string $to): string
    {
        $from = self::normalize($from);
        $to = self::normalize($to);

        if ('/' !== $from[0]) {
            throw new InvalidArgumentException('$from argument is not an absolute path');
        }

        if ('/' !== $to[0]) {
            throw new InvalidArgumentException('$to argument is not an absolute path');
        }

        $from = explode('/', rtrim($from, '/'));
        $to = explode('/', rtrim($to, '/'));
        $relPath = $to;

        $fromAmount = count($from);

        foreach ($from as $depth => $dir) {
            if ($dir === $to[$depth]) {
                array_shift($relPath);
                continue;
            }

            $remaining = $fromAmount - $depth;

            if ($remaining > 1) {
                $relPath = array_pad(
                    $relPath,
                    (count($relPath) + $remaining - 1) * -1,
                    '..'
                );
                break;
            }

            $relPath[0] = sprintf('.%s%s', \DIRECTORY_SEPARATOR, $relPath[0]);
        }

        return implode(\DIRECTORY_SEPARATOR, $relPath);
    }
}
