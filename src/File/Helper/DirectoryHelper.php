<?php declare(strict_types=1);

namespace LDL\File\Helper;

use LDL\File\Directory;
use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileTypeException;
use LDL\File\Exception\FileReadException;
use LDL\File\Exception\FileWriteException;
use LDL\File\File;
use LDL\File\FileTree;

final class DirectoryHelper
{
    /**
     * @param string $path
     * @param int $mode
     * @return Directory
     * @throws FileExistsException
     * @throws FileWriteException
     * @throws FileTypeException
     */
    public static function create(string $path, int $mode) : Directory
    {
        if(is_dir($path)){
            throw new FileExistsException("Directory \"$path\" already exists!");
        }

        if(!@mkdir($path, $mode) && !is_dir($path)){
            throw new FileWriteException("Unable to create directory: $path");
        }

        return new Directory($path);
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
     * @throws FileTypeException
     * @throws FileReadException
     * @throws FileWriteException
     */
    public static function delete(string $dir) : bool
    {
        if(!is_dir($dir)){
            throw new FileTypeException("Given path \"$dir\" is not a directory!");
        }

        if(!is_writable($dir)){
            throw new FileWriteException("Directory \"$dir\" is not writable!");
        }

        $files = scandir($dir);

        foreach($files as $file){
            if('.' === $file || '..' === $file){
                continue;
            }

            $file = sprintf('%s%s%s', $dir, \DIRECTORY_SEPARATOR, $file);

            is_dir($file) ? self::delete($file) : unlink($file);
        }

        return rmdir($dir);
    }

    public static function copy(string $source, string $dest) : Directory
    {
        throw new \LogicException('@TODO Copy directory recursively');
    }

    /**
     * Obtains a FileTree
     *
     * @param string $path
     * @return FileTree
     * @throws FileExistsException
     * @throws FileReadException
     * @throws FileTypeException
     */
    public static function getTree(string $path) : FileTree
    {
        $dir = new Directory($path);

        if(!$dir->isReadable()){
            throw new FileReadException("Directory \"$path\" is not readable!");
        }

        $tree = new FileTree($dir);

        foreach(scandir($path) as $file){
            if('.' === $file || '..' === $file){
                continue;
            }
            $file = FilePathHelper::createAbsolutePath($dir->toString(), $file);
            $tree->append(!is_file($file) ? new Directory($file) : new File($file));
        }

        return $tree;
    }

    /**
     * @param string $path
     * @return iterable
     * @throws FileExistsException
     * @throws FileReadException
     * @throws FileTypeException
     */
    public static function iterateTree(string $path) : iterable
    {
        $dir = new Directory($path);

        if(!$dir->isReadable()){
            throw new FileReadException("Directory $dir is not readable!");
        }

        foreach(scandir($path) as $file){
            if('.' === $file || '..' === $file){
                continue;
            }
            $file = FilePathHelper::createAbsolutePath($dir->toString(), $file);
            yield is_dir($file) ? new Directory($file) : new File($file);
        }
    }
}