<?php declare(strict_types=1);

namespace LDL\File\Helper;

use LDL\File\Exception\ExistsException;
use LDL\File\Exception\ReadException;

final class FileTypeHelper
{
    public const FILE_TYPE_REGULAR='regular';
    public const FILE_TYPE_DIRECTORY='directory';
    public const FILE_TYPE_LINK='link';
    public const FILE_TYPE_SOCKET='socket';
    public const FILE_TYPE_FIFO='fifo';
    public const FILE_TYPE_CHAR='char';
    public const FILE_TYPE_BLOCK='block';
    public const FILE_TYPE_UNKNOWN='unknown';

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
                return self::FILE_TYPE_SOCKET;
                break;
            case 0xA000: // symbolic link
                return self::FILE_TYPE_LINK;
                break;
            case 0x8000: // regular
                return self::FILE_TYPE_REGULAR;
                break;
            case 0x6000: // block special
                return self::FILE_TYPE_BLOCK;
                break;
            case 0x4000: // directory
                return self::FILE_TYPE_DIRECTORY;
                break;
            case 0x2000: // character special
                return self::FILE_TYPE_CHAR;
                break;
            case 0x1000: // FIFO pipe
                return self::FILE_TYPE_FIFO;
                break;
            default: // unknown
                return self::FILE_TYPE_UNKNOWN;
                break;
        }

    }
}