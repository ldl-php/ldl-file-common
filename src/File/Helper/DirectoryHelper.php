<?php

namespace LDL\File\Helper;

final class DirectoryHelper
{
     /**
     * Given a directory path, it will check if its empty or not.
     * If the directory path is not empty, delete all files including hidden files.
     * if the directory path is empty, delete the directory
     *
     * @param string $dir
     */


    public static function deleteDirectory(string $dir, bool $isEmpty=false)
    {

            
        $files = scandir($dir);

        foreach($files as $file){
            if('.' === $file || '..' === $file){
                continue;
            }
            
            echo $file;
            echo "\n";
            echo "Operation: \"$file\", has been deleted\n";
            echo "\n";
            is_dir("$dir/$file") ? self::deleteDirectory("$dir/$file") : unlink("$dir/$file");
        }

        return rmdir($dir);
        


    }

}