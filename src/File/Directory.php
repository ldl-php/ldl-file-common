<?php declare(strict_types=1);

namespace LDL\File;

use LDL\File\Constants\FileTypeConstants;
use LDL\File\Contracts\DirectoryInterface;
use LDL\File\Contracts\FileInterface;
use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileTypeException;
use LDL\File\Exception\FileWriteException;
use LDL\File\Helper\DirectoryHelper;
use LDL\File\Helper\FilePathHelper;
use LDL\File\Helper\FilePermsHelper;

final class Directory implements DirectoryInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var bool
     */
    private $isDeleted=false;

    /**
     * Directory constructor.
     * @param string $path
     * @throws FileTypeException
     */
    public function __construct(string $path)
    {

        if(is_dir($path)){
            $this->path = $path;
            return;
        }

        throw new FileTypeException(
            sprintf(
                'File %s does not exists or is not a directory, if you wish to create it, use %s::create',
                $path,
                __CLASS__
            )
        );
    }

    public function getType() : string
    {
        return FileTypeConstants::FILE_TYPE_DIRECTORY;
    }

    public function rename(string $name): DirectoryInterface
    {
        throw new \RuntimeException('@TODO Rename directory');
    }

    public function isReadable() : bool
    {
        return is_readable($this->path);
    }

    public function isWritable() : bool
    {
        return is_writable($this->path);
    }

    public static function create(string $path, int $permissions=0755) : DirectoryInterface
    {
        return DirectoryHelper::create($path, $permissions);
    }

    /**
     * Creates a directory inside of the current directory
     *
     * @param string $path
     * @param int $permissions
     * @return Directory
     * @throws FileExistsException
     * @throws FileWriteException
     * @throws FileTypeException
     */
    public function mkdir(string $path, int $permissions=0755) : DirectoryInterface
    {
        if(!$this->isWritable()){
            throw new FileWriteException("Directory {$this->path} is not writable!");
        }

        $path = FilePathHelper::createAbsolutePath($this->path, $path);
        return DirectoryHelper::create($path, $permissions);
    }

    /**
     * {@inheritdoc}
     */
    public function mkfile(
        string $name,
        string $contents=null,
        int $permissions=0644
    ) : FileInterface
    {
        $name = basename($name);

        return File::create(
            FilePathHelper::createAbsolutePath($this->path, $name),
            $contents,
            $permissions
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTree() : FileTree
    {
        $this->checkIfDeleted(__METHOD__);
        return DirectoryHelper::getTree($this->path);
    }

    /**
     * {@inheritdoc}
     */
    public function iterateTree() : iterable
    {
        $this->checkIfDeleted(__METHOD__);
        yield from DirectoryHelper::iterateTree($this->toString());
    }

    /**
     * {@inheritdoc}
     */
    public function delete() : DirectoryInterface
    {
        $this->checkIfDeleted(__METHOD__);

        DirectoryHelper::delete($this->path);
        $this->isDeleted = true;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function chmod(int $permissions) : DirectoryInterface
    {
        $this->checkIfDeleted(__METHOD__);

        FilePermsHelper::chmod($this->path, $permissions);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPerms() : int
    {
        return FilePermsHelper::getPerms($this->toString());
    }

    /**
     * {@inheritdoc}
     */
    public function copy(string $dest) : DirectoryInterface
    {
        $this->checkIfDeleted(__METHOD__);
        return DirectoryHelper::copy($this->toString(), $dest);
    }

    /**
     * {@inheritdoc}
     */
    public function move(): DirectoryInterface
    {
        throw new \LogicException('@TODO');
    }

    /**
     * @return bool
     */
    public function isDeleted() : bool
    {
        return $this->isDeleted;
    }

    //<editor-fold desc="ToStringInterface Methods">
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

        $msg = sprintf('Can not %s directory "%s", it has been deleted', $operation, $this->path);

        throw new FileExistsException($msg);
    }
    //</editor-fold>

}