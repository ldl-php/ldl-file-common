<?php declare(strict_types=1);

namespace LDL\File\Helper;

final class PermissionsHelper
{
    /**
     * @param string $file
     * @param int $permissions
     * @throws \InvalidArgumentException if the file does not exists
     * @throws \RuntimeException if file permissions could not be modified
     */
    public static function chmod(string $file, int $permissions) : void
    {
        if(!file_exists($file)){
            throw new \InvalidArgumentException("File \"$file\" does not exists");
        }

        $result = chmod($file, $permissions);

        if(!$result){
            throw new \RuntimeException(
                sprintf(
                    'Failed to change file permissions to: %s',
                    var_export($permissions, true)
                )
            );
        }
    }

}