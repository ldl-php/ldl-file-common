<?php declare(strict_types=1);

namespace LDL\File\Contracts;

use LDL\File\Constants\FileTypeConstants;
use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileReadException;
use LDL\File\Exception\FileTypeException;
use LDL\File\Exception\FileWriteException;
use LDL\Framework\Base\Contracts\Type\ToStringInterface;
use LDL\Type\Collection\Types\String\StringCollection;

interface FileInterface extends ToStringInterface, ReadFileLinesInterface
{
    /**
     * Returns full path to the file including file name
     *
     * @return string
     */
    public function getPath() : string;

    /**
     * Returns file extension
     * @return string
     */
    public function getExtension() : string;

    /**
     * Obtains the directory where this file is located
     *
     * @param int|null $levels
     *
     * @return DirectoryInterface
     * @throws FileTypeException
     * @throws FileExistsException
     */
    public function getDirectory(int $levels=null) : DirectoryInterface;

    /**
     * Returns lines from the file as an LDL StringCollection
     *
     * @return StringCollection
     */
    public function getLines() : StringCollection;

    /**
     * Returns lines from the file as a PHP string
     * 
     * @return string
     */
    public function getLinesAsString() : string;

    /**
     * Creates a new file
     *
     * @param string $file
     * @param string|null $contents
     * @param int|null $permissions
     * @param bool|null $overwrite
     *
     * @return FileInterface
     *
     * @throws FileExistsException
     * @throws FileTypeException
     * @throws FileWriteException
     */
    public static function create(
        string $file,
        ?string $contents,
        ?int $permissions,
        ?bool $overwrite
    ) : FileInterface;

    /**
     * @param string $dest
     * @param bool $overwrite
     * @return FileInterface
     * @throws FileExistsException
     */
    public function copy(string $dest, bool $overwrite) : FileInterface;

    /**
     * Moves current file into a new directory
     *
     * @param DirectoryInterface $to
     * @throws FileReadException
     * @throws FileWriteException
     * @return FileInterface
     */
    public function move(DirectoryInterface $to) : FileInterface;

    /**
     * Changes name of current file
     *
     * @param string $name
     * @return FileInterface
     * @throws FileWriteException
     */
    public function rename(string $name): FileInterface;

    /**
     * @param int $permissions In octal mode
     * @throws FileWriteException
     * @throws FileExistsException
     * @return FileInterface
     */
    public function chmod(int $permissions) : FileInterface;

    /**
     * Deletes a file, if the file is a directory the directory must be recursively deleted
     *
     * @return FileInterface
     * @throws FileExistsException
     * @throws FileTypeException
     * @throws FileReadException
     * @throws FileWriteException
     */
    public function delete() : FileInterface;

    /**
     * @param string $contents
     * @param string|null $newName
     * @return FileInterface
     * @throws FileTypeException
     * @throws FileWriteException
     * @throws FileExistsException
     */
    public function put(string $contents, string $newName=null) : FileInterface;

    /**
     * @param string $content
     * @return FileInterface
     */
    public function append(string $content) : FileInterface;

    /**
     * Returns file permissions in octal mode
     *
     * @throws FileExistsException
     * @return int
     */
    public function getPerms() : int;

    /**
     * @return bool
     */
    public function isDeleted() : bool;

    /**
     * @return bool
     */
    public function isWritable() : bool;

    /**
     * @return bool
     */
    public function isReadable() : bool;

    /**
     * Returns file type
     *
     * @see FileTypeConstants
     * @return string
     *
     * @throws FileExistsException
     * @throws FileReadException
     *
     */
    public function getType() : string;
}
