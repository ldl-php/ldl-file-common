<?php declare(strict_types=1);

namespace LDL\File\Helper;

use LDL\File\Exception\ExistsException;
use LDL\File\Exception\ReadException;
use LDL\File\Constants\FileTypeConstants;

final class FileTypeHelper
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
}