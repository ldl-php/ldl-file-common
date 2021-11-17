<?php declare(strict_types=1);

namespace LDL\File;

use LDL\File\Collection\Contracts\ReadFileLinesInterface;
use LDL\File\Exception\ExistsException;
use LDL\File\Exception\FileTypeException;
use LDL\File\Exception\ReadException;
use LDL\File\Exception\WriteException;
use LDL\File\Helper\FileHelper;
use LDL\File\Helper\PermissionsHelper;
use LDL\Framework\Base\Contracts\Type\ToStringInterface;
use LDL\Type\Collection\Types\String\StringCollection;

final class File extends \SplFileInfo implements ToStringInterface, ReadFileLinesInterface
{

    /**
     * @var bool
     */
    private $isDeleted;

    /**
     * When calling from the constructor, the file must always exist, if the file is to be created
     * the self::create method must be called.
     *
     * @param string $file
     * @throws FileTypeException
     * @throws ExistsException
     */
    public function __construct(string $file)
    {
        $exists = file_exists($file);
        $isDir = is_dir($file);

        if($exists && !$isDir){
            parent::__construct($file);
            return;
        }

        if(!$exists){
            throw new ExistsException(
                sprintf(
                    'File %s does not exists, if you want to create it please use: %s::%s',
                    $file,
                    __CLASS__,
                    'create'
                )
            );
        }

        if($isDir){
            throw new FileTypeException(
                sprintf(
                    'Invalid file type provided, expected file, got directory: "%s"',
                    $file
                )
            );
        }
    }

    /**
     * @param string $file
     * @param string|null $contents
     * @param int $permissions
     * @return File
     * @throws FileTypeException
     * @throws ReadException
     * @throws WriteException
     */
    public static function create(string $file, string $contents=null, int $permissions=0644) : File
    {
        if(is_dir($file)){
            throw new \InvalidArgumentException("Given file path \"$file\" is a directory");
        }

        if(file_exists($file)){
            throw new \InvalidArgumentException("File \"$file\" already exists!");
        }

        $fp = fopen($file, 'wb');
        fwrite($fp, (string)$contents);
        fclose($fp);

        return (new self($file))
            ->put($contents)
            ->chmod($permissions);
    }

    /**
     * @param int $permissions
     * @return File
     * @throws WriteException
     * @throws ExistsException
     */
    public function chmod(int $permissions) : File
    {
        $this->checkIfDeleted(__METHOD__);

        if(!$this->isWritable()){
            throw new WriteException("File \"$this\" is not writable!");
        }

        PermissionsHelper::chmod($this->toString(), $permissions);
        return $this;
    }

    /**
     * @return StringCollection
     * @throws ReadException
     * @throws FileTypeException
     * @throws ExistsException
     */
    public function getLines() : StringCollection
    {
        $this->checkIfDeleted(__METHOD__);
        return FileHelper::getLines($this->toString());
    }

    /**
     * @return iterable
     * @throws FileTypeException
     * @throws ReadException
     * @throws ExistsException
     */
    public function iterateLines(): iterable
    {
        $this->checkIfDeleted(__METHOD__);
        yield from FileHelper::iterateLines($this->toString());
    }

    /**
     * @param string $contents
     * @param string|null $newName
     * @return File
     * @throws FileTypeException
     * @throws ReadException
     * @throws WriteException
     */
    public function put(string $contents, string $newName=null) : File
    {
        $file = trim($newName ?? $this->toString());

        if(!is_writable($file)){
            throw new WriteException("File \"$file\" is not writable");
        }

        if('' === $file){
            throw new \InvalidArgumentException('File name can not be empty');
        }

        file_put_contents($file, $contents);

        return new self($file);
    }

    /**
     * Appends $content to the file, if the file has been deleted an exception will be thrown
     * @param string $content
     * @return File
     * @throws WriteException
     * @throws ExistsException
     */
    public function append(string $content) : File
    {
        $this->checkIfDeleted(__METHOD__);

        if(!is_writable($this->toString())){
            throw new WriteException("File \"$this\" is not writable");
        }

        file_put_contents($this->toString(), $content, \FILE_APPEND);
        return $this;
    }

    /**
     * @param string $dest
     * @param bool $overwrite
     * @return File
     * @throws FileTypeException
     * @throws ReadException
     * @throws WriteException
     */
    public function copy(string $dest, bool $overwrite) : File
    {
        FileHelper::copy($this->toString(), $dest, $overwrite);
        return new self($dest);
    }

    /**
     * Obtains the directory where this file is located
     *
     * @return Directory
     * @throws FileTypeException
     * @throws ExistsException
     */
    public function getDirectory() : Directory
    {
        $this->checkIfDeleted(__METHOD__);
        return new Directory($this->getPath());
    }

    /**
     * @return bool
     */
    public function isDeleted() : bool
    {
        return $this->isDeleted;
    }

    //<editor-fold desc="ToStringInterface methods">
    public function toString(): string
    {
        return (string)$this->getRealPath();
    }

    public function __toString(): string
    {
        return $this->toString();
    }
    //</editor-fold>

    //<editor-fold desc="Private methods">

    /**
     * @param string $operation
     * @throws ExistsException
     */
    private function checkIfDeleted(string $operation) : void
    {
        if(!$this->isDeleted){
            return;
        }

        $msg = sprintf('Can not %s from file "%s", it has been deleted', $operation, $this->toString());

        throw new ExistsException($msg);
    }
    //</editor-fold>
}