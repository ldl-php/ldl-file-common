<?php declare(strict_types=1);

namespace LDL\File\Helper;

use LDL\File\Directory;
use LDL\File\Exception\ExistsException;
use LDL\File\Exception\FileTypeException;
use LDL\File\Exception\ReadException;
use LDL\File\Exception\WriteException;
use LDL\File\File;
use LDL\File\Tree;

final class DirectoryHelper
{
    /**
     * @param string $path
     * @param int $mode
     * @return Directory
     * @throws ExistsException
     * @throws WriteException
     * @throws FileTypeException
     */
    public static function create(string $path, int $mode) : Directory
    {
        if(is_dir($path)){
            throw new ExistsException("Directory \"$path\" already exists!");
        }

        if(!@mkdir($path, $mode) && !is_dir($path)){
            throw new WriteException("Unable to create directory: $path");
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
     * @throws ReadException
     * @throws WriteException
     */
    public static function delete(string $dir) : bool
    {
        if(!is_dir($dir)){
            throw new FileTypeException("Given path \"$dir\" is not a directory!");
        }

        if(!is_readable($dir)){
            throw new ReadException("Directory \"$dir\" is not readable!");
        }

        if(!is_writable($dir)){
            throw new WriteException("Directory \"$dir\" is not writable!");
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
        throw new \Exception('@TODO Copy directory recursively');
    }

    /**
     * @param string $path
     * @return Tree
     * @throws FileTypeException
     * @throws ReadException
     */
    public static function getTree(string $path) : Tree
    {
        $dir = new Directory($path);

        if(!$dir->isReadable()){
            throw new ReadException("Directory \"$path\" is not readable!");
        }

        $tree = new Tree($dir);

        foreach(scandir($path) as $file){
            if('.' === $file || '..' === $file){
                continue;
            }
            $file = PathHelper::createAbsolutePath($dir->toString(), $file);
            $tree->append(is_dir($file) ? new Directory($file) : new File($file));
        }

        return $tree;
    }

    /**
     * @param string $path
     * @return iterable
     * @throws FileTypeException
     * @throws ReadException
     */
    public static function iterateTree(string $path) : iterable
    {
        $dir = new Directory($path);

        if(!$dir->isReadable()){
            throw new ReadException("Directory $dir is not readable!");
        }

        foreach(scandir($path) as $file){
            if('.' === $file || '..' === $file){
                continue;
            }
            $file = PathHelper::createAbsolutePath($dir->toString(), $file);
            yield is_dir($file) ? new Directory($file) : new File($file);
        }
    }
}