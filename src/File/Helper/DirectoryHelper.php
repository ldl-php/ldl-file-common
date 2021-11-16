<?php declare(strict_types=1);

namespace LDL\File\Helper;

final class DirectoryHelper
{
    /**
     * @param string $path
     * @param int $mode
     * @throws \Exception
     */
    public static function create(string $path, int $mode) : void
    {
        if(!@mkdir($path, $mode) && !is_dir($path)){
            throw new \RuntimeException("Unable to create directory: $path");
        }
    }

    /**
     * Given a directory path, it will check if its empty or not.
     * If the directory path is not empty, delete all files including hidden files.
     * if the directory path is empty, delete the directory
     *
     * @author Ohiare Nathaniel
     *
     * @param string $dir
     * @return bool
     */
    public static function delete(string $dir) : bool
    {
        $files = scandir($dir);

        foreach($files as $file){
            if('.' === $file || '..' === $file){
                continue;
            }

            $file = sprintf('%s%s%s', $dir, \DIRECTORY_SEPARATOR, $file);

            is_dir($file) ? self::delete("$dir/$file") : unlink($file);
        }

        return rmdir($dir);
    }


}