<?php declare(strict_types=1);

namespace LDL\File\Contracts;

use LDL\File\Constants\FileTypeConstants;
use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileReadException;
use LDL\File\Exception\FileTypeException;
use LDL\File\Exception\FileWriteException;
use LDL\Framework\Base\Contracts\Type\ToStringInterface;

interface FileInterface extends ToStringInterface
{
    /**
     * Creates a new file
     *
     * @param string $file
     * @param string|null $contents
     * @param int|null $permissions
     * @return FileInterface
     * @throws FileExistsException
     * @throws FileTypeException
     * @throws FileWriteException
     */
    public static function create(
        string $file,
        ?string $contents,
        ?int $permissions
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
