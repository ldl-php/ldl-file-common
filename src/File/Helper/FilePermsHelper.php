<?php declare(strict_types=1);

namespace LDL\File\Helper;

use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileWriteException;

final class FilePermsHelper
{

    /**
     * @param string $file
     * @param int $permissions
     * @throws FileExistsException
     * @throws FileWriteException
     */
    public static function chmod(string $file, int $permissions) : void
    {
        if(!file_exists($file)){
            throw new FileExistsException("File \"$file\" does not exists");
        }

        $result = @chmod($file, $permissions);

        if(!$result){
            throw new FileWriteException(
                sprintf(
                    'Failed to change file permissions to: %s',
                    var_export($permissions, true)
                )
            );
        }
    }

    /**
     * @param string $file
     * @return int
     * @throws FileExistsException
     */
    public static function getPerms(string $file) : int
    {
        if(!file_exists($file)){
            throw new FileExistsException("File $file does not exists!");
        }

        return fileperms($file);
    }

}