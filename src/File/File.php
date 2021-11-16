<?php declare(strict_types=1);

namespace LDL\File;

use LDL\File\Collection\Contracts\ReadFileLinesInterface;
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
     */
    public function __construct(string $file)
    {
        if(file_exists($file) && !is_dir($file)){
            parent::__construct($file);
            return;
        }

        throw new \InvalidArgumentException(
            sprintf(
                'File %s does not exists or is a directory, if you wish to create it, use %s::create',
                $file,
                __CLASS__
            )
        );
    }

    /**
     * @param string $file
     * @param int $permissions
     * @param string|null $contents
     * @return File
     */
    public static function create(string $file, string $contents=null, int $permissions=0644) : File
    {
        if(is_dir($file)){
            throw new \InvalidArgumentException("Given file path \"$file\" is a directory");
        }

        if(file_exists($file)){
            throw new \InvalidArgumentException("File \"$file\" already exists!");
        }

        $fp = fopen($file, '+wb');
        fwrite($fp, (string)$contents);
        fclose($fp);

        return (new self($file))
            ->put($contents)
            ->chmod($permissions);
    }

    /**
     * @param int $permissions
     * @return File
     */
    public function chmod(int $permissions) : File
    {
        $this->checkIfDeleted(__METHOD__);
        PermissionsHelper::chmod($this->toString(), $permissions);
        return $this;
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

    public function put(string $contents, string $newName=null) : File
    {
        $file = trim($newName ?? $this->toString());

        if('' === $file){
            throw new \RuntimeException('File name can not be empty');
        }

        file_put_contents($file, $contents);

        return new self($file);
    }

    /**
     * Appends $content to the file, if the file has been deleted an exception will be thrown
     * @param string $content
     * @return File
     */
    public function append(string $content) : File
    {
        $this->checkIfDeleted(__METHOD__);
        file_put_contents($this->toString(), $content, \FILE_APPEND);
        return $this;
    }

    /**
     * @param string $dest
     * @param bool $overwrite
     * @throws \RuntimeException
     * @return File
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
     */
    public function getDirectory() : Directory
    {
        $this->checkIfDeleted(__METHOD__);
        return new Directory($this->getPath());
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
     * @throws \RuntimeException
     */
    private function checkIfDeleted(string $operation) : void
    {
        if(!$this->isDeleted){
            return;
        }

        $msg = sprintf('Can not %s from file "%s", it has been deleted', $operation, $this->toString());

        throw new \RuntimeException($msg);
    }
    //</editor-fold>
}