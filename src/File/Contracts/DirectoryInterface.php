<?php declare(strict_types=1);

namespace LDL\File\Contracts;

use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileReadException;
use LDL\File\Exception\FileTypeException;
use LDL\File\Exception\FileWriteException;
use LDL\File\FileTree;
use LDL\Framework\Base\Contracts\Type\ToStringInterface;

interface DirectoryInterface extends ToStringInterface
{
    /**
     * @return string
     */
    public function getPath() : string;

    /**
     * Creates a new directory and returns a DirectoryInterface instance
     *
     * @throws FileWriteException
     * @throws FileExistsException
     * @param string $path
     * @param int $permissions
     * @return DirectoryInterface
     */
    public static function create(string $path, int $permissions=0755) : DirectoryInterface;

    /**
     * @param string $dest
     * @return DirectoryInterface
     * @throws FileExistsException
     */
    public function copy(string $dest) : DirectoryInterface;

    /**
     * @throws FileReadException
     * @throws FileWriteException
     * @return DirectoryInterface
     */
    public function move() : DirectoryInterface;

    /**
     * Change access permissions on a directory
     *
     * @param int $permissions In octal mode
     * @throws FileWriteException
     * @throws FileExistsException
     * @return DirectoryInterface
     */
    public function chmod(int $permissions) : DirectoryInterface;

    /**
     * Deletes a directory, the directory will be recursively deleted
     *
     * @return DirectoryInterface
     * @throws FileExistsException
     * @throws FileTypeException
     * @throws FileReadException
     * @throws FileWriteException
     */
    public function delete() : DirectoryInterface;

    /**
     * Returns a Tree (containing FileInterface objects and DirectoryInterface objects)
     *
     * @return FileTree
     * @throws FileReadException
     * @throws FileTypeException
     * @throws FileExistsException
     */
    public function getTree() : FileTree;

    /**
     * @return iterable
     * @throws FileReadException
     * @throws FileTypeException
     * @throws FileExistsException
     */
    public function iterateTree() : iterable;

    /**
     * Creates a directory inside of the current directory
     *
     * @param string $path
     * @param int $permissions
     * @return DirectoryInterface
     * @throws FileExistsException
     * @throws FileWriteException
     * @throws FileTypeException
     */
    public function mkdir(string $path, int $permissions=0755) : DirectoryInterface;

    /**
     * Creates a new file in the current directory
     *
     * @param string $name
     * @param string|null $contents
     * @param int $permissions
     * @return FileInterface
     * @throws FileExistsException
     * @throws FileTypeException
     * @throws FileWriteException
     */
    public function mkfile(
        string $name,
        string $contents=null,
        int $permissions=0644
    ) : FileInterface;

    /**
     * @param string $name
     * @throws FileWriteException
     * @return DirectoryInterface
     */
    public function rename(string $name) : DirectoryInterface;
}
