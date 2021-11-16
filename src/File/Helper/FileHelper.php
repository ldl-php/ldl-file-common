<?php declare(strict_types=1);

namespace LDL\File\Helper;

use LDL\File\Exception\ExistsException;
use LDL\File\Exception\ReadException;
use LDL\File\Constants\FileTypeConstants;
use LDL\Type\Collection\Types\String\StringCollection;

final class FileHelper
{
    /**
     * @param string $path
     * @throws ExistsException if file does not exists
     * @throws ReadException if file permissions could not be read
     * @return string
     */
    public static function getType(string $path) : string
    {
        if(!file_exists($path)){
            throw new ExistsException('Can not get permissions from a file which does not exists');
        }

        $perms = fileperms($path);

        if(false === $perms){
            throw new ReadException('Could not get file permissions');
        }

        switch ($perms & 0xF000) {
            case 0xC000: // socket
                return FileTypeConstants::FILE_TYPE_SOCKET;
                break;
            case 0xA000: // symbolic link
                return FileTypeConstants::FILE_TYPE_LINK;
                break;
            case 0x8000: // regular
                return FileTypeConstants::FILE_TYPE_REGULAR;
                break;
            case 0x6000: // block special
                return FileTypeConstants::FILE_TYPE_BLOCK;
                break;
            case 0x4000: // directory
                return FileTypeConstants::FILE_TYPE_DIRECTORY;
                break;
            case 0x2000: // character special
                return FileTypeConstants::FILE_TYPE_CHAR;
                break;
            case 0x1000: // FIFO pipe
                return FileTypeConstants::FILE_TYPE_FIFO;
                break;
            default: // unknown
                return FileTypeConstants::FILE_TYPE_UNKNOWN;
                break;
        }

    }

    /**
     * Read lines from a file
     *
     * @param string $file
     * @return StringCollection
     */
    public static function getLines(string $file) : StringCollection
    {
        if(!is_readable($file)){
            throw new \RuntimeException("File $file is not readable!");
        }

        if(is_dir($file)){
            throw new \RuntimeException("$file is a directory");
        }

        return new StringCollection(file($file));
    }

    /**
     * Iterate through the lines of a file in a memory efficient way by using generators
     * Useful to traverse large files line by line.
     *
     * @param string $file
     * @return iterable
     */
    public static function iterateLines(string $file) : iterable
    {
        if(!is_readable($file)){
            throw new \RuntimeException("File $file is not readable!");
        }

        if(is_dir($file)){
            throw new \RuntimeException("$file is a directory");
        }

        $fp = fopen($file, 'rb');

        while($line = fgets($fp)){
            yield $line;
        }

        fclose($fp);
    }

    /**
     * Copy a file from $source to $dest, if the $dest file exists, and the overwrite flag is set to true
     * the file will be overwritten.
     *
     * @param string $source
     * @param string $dest
     * @param bool $overwrite
     *
     * @throws \RuntimeException
     */
    public static function copy(string $source, string $dest, bool $overwrite=false) : void
    {
        if(false === $overwrite && file_exists($dest)){
            $msg = sprintf(
                'Destination file %s already exists, if you really want to overwrite it, set the overwrite flag to true',
                $dest
            );
            throw new \RuntimeException($msg);
        }

        if(!copy($source, $dest)){
            throw new \RuntimeException('Could not copy file from "%s" to "%s"', $source, $dest);
        }
    }

}