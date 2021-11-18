<?php declare(strict_types=1);

namespace LDL\File;

use LDL\File\Collection\Contracts\ReadFileLinesInterface;
use LDL\File\Contracts\DirectoryInterface;
use LDL\File\Contracts\FileInterface;
use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileTypeException;
use LDL\File\Exception\FileWriteException;
use LDL\File\Helper\FileHelper;
use LDL\File\Helper\FilePermsHelper;
use LDL\Type\Collection\Types\String\StringCollection;

final class File implements FileInterface, ReadFileLinesInterface
{

    /**
     * @var string
     */
    private $path;

    /**
     * @var bool
     */
    private $isDeleted;

    /**
     * When calling from the constructor, the file must always exist, if the file is to be created
     * the self::create method must be called.
     *
     * @param string $file
     * @throws FileExistsException
     * @throws FileTypeException
     */
    public function __construct(string $file)
    {
        $this->path = realpath($file);

        if(!$this->path){
            throw new FileExistsException(
                sprintf(
                    'File %s does not exists, if you want to create it please use: %s::%s',
                    $file,
                    __CLASS__,
                    'create'
                )
            );
        }

        if(is_dir($this->path)){
            throw new FileTypeException(
                sprintf(
                    'Invalid file type provided, expected file, got directory: "%s"',
                    $file
                )
            );
        }
    }

    public function delete() : FileInterface
    {
        FileHelper::delete($this->toString());
        $this->isDeleted = true;
        return $this;
    }

    public function getType() : string
    {
        return FileHelper::getType((string)$this);
    }

    public static function create(
        string $file,
        ?string $contents,
        ?int $permissions
    ) : FileInterface
    {
        $permissions = $permissions ?? 0644;
        $contents = $contents ?? '';

        if(file_exists($file)){
            throw new FileExistsException("File \"$file\" already exists!");
        }

        if(false === file_put_contents($file, $contents)){
            throw new FileWriteException("Failed to create file: $file");
        }

        return (new self($file))
            ->chmod($permissions);
    }

    public function move(DirectoryInterface $to) : FileInterface
    {
        throw new \RuntimeException('@TODO move file to directory');
    }

    public function rename(string $name): FileInterface
    {
        throw new \RuntimeException('@TODO rename file');
    }

    public function chmod(int $permissions) : FileInterface
    {
        $this->checkIfDeleted(__METHOD__);
        FilePermsHelper::chmod($this->toString(), $permissions);
        return $this;
    }

    public function getPerms() : int
    {
        return FilePermsHelper::getPerms($this->toString());
    }

    public function getLines() : StringCollection
    {
        $this->checkIfDeleted(__METHOD__);
        return FileHelper::getLines($this->toString());
    }

    public function iterateLines(): iterable
    {
        $this->checkIfDeleted(__METHOD__);
        yield from FileHelper::iterateLines($this->toString());
    }

    public function put(string $contents, string $newName=null) : FileInterface
    {
        $file = trim($newName ?? $this->toString());

        if(!is_writable($file)){
            throw new FileWriteException("File \"$file\" is not writable");
        }

        if('' === $file){
            throw new \InvalidArgumentException('File name can not be empty');
        }

        if(false === file_put_contents($file, $contents)){
            throw new FileWriteException("Failed to write to file: {$this->path}");
        }

        return new self($file);
    }

    public function append(string $content) : File
    {
        $this->checkIfDeleted(__METHOD__);

        if(!is_writable($this->toString())){
            throw new FileWriteException("File \"$this\" is not writable");
        }

        file_put_contents($this->toString(), $content, \FILE_APPEND);
        return $this;
    }

    public function copy(string $dest, bool $overwrite) : FileInterface
    {
        FileHelper::copy($this->toString(), $dest, $overwrite);
        return new self($dest);
    }

    /**
     * Obtains the directory where this file is located
     *
     * @return Directory
     * @throws FileTypeException
     * @throws FileExistsException
     */
    public function getDirectory() : Directory
    {
        $this->checkIfDeleted(__METHOD__);
        return new Directory($this->getPath());
    }

    public function isDeleted() : bool
    {
        return $this->isDeleted;
    }

    public function isReadable(): bool
    {
        return is_readable($this->path);
    }

    public function isWritable(): bool
    {
        return is_writable($this->path);
    }

    //<editor-fold desc="ToStringInterface methods">
    public function toString(): string
    {
        return $this->path;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
    //</editor-fold>

    //<editor-fold desc="Private methods">

    /**
     * @param string $operation
     * @throws FileExistsException
     */
    private function checkIfDeleted(string $operation) : void
    {
        if(!$this->isDeleted){
            return;
        }

        $msg = sprintf('Can not %s from file "%s", it has been deleted', $operation, $this->toString());

        throw new FileExistsException($msg);
    }
    //</editor-fold>
}