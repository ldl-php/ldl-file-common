<?php declare(strict_types=1);

namespace LDL\File\Helper;

final class PathHelper
{
    /**
     * Given a relative path, it will return it's absolute path, even if the path does not exists
     * If the flag $mustExist is set to true, realpath will be used (which checks the existence of the given $path)
     * if the path does not exists, an \InvalidArgumentException will be thrown.
     *
     * @param string $path
     * @param bool $mustExist
     * @throws \InvalidArgumentException
     * @return string
     */
    public static function getAbsolutePath(string $path, bool $mustExist=false) : ?string
    {
        if($mustExist){
            $realPath = realpath($path);
            if(false === $realPath){
                throw new \InvalidArgumentException("Given path: \"$path\" does not exist");
            }

            return $realPath;
        }

        $path = str_replace(['/', '\\'], \DIRECTORY_SEPARATOR, $path);

        $parts = array_filter(explode(\DIRECTORY_SEPARATOR, $path), static function($p){
            return '' !== $p;
        });

        $absolutes = [];

        foreach ($parts as $part) {
            if ('.' === $part){
                continue;
            }

            '..' === $part ? array_pop($absolutes) : $absolutes[] = $part;
        }

        return sprintf('%s%s', \DIRECTORY_SEPARATOR, implode(\DIRECTORY_SEPARATOR, $absolutes));
    }

    /**
     * Creates a path string from a list of variable arguments
     * Path does not have to exist in order to be returned.
     *
     * @param mixed ...$parts
     * @return string
     */
    public static function createAbsolutePath(...$parts) : string
    {
        return self::getAbsolutePath(implode(\DIRECTORY_SEPARATOR, $parts));
    }
}