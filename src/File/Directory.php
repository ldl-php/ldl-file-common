<?php declare(strict_types=1);

namespace LDL\File;

use LDL\File\Exception\ExistsException;
use LDL\File\Exception\FileTypeException;
use LDL\File\Exception\ReadException;
use LDL\File\Exception\WriteException;
use LDL\File\Helper\DirectoryHelper;
use LDL\File\Helper\PathHelper;
use LDL\File\Helper\PermissionsHelper;
use LDL\Framework\Base\Contracts\Type\ToStringInterface;

final class Directory implements ToStringInterface
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
                'File %s does not exists or is a directory, if you wish to create it, use %s::create',
                $path,
                __CLASS__
            )
        );
    }

    /**
     * @return bool
     */
    public function isReadable() : bool
    {
        return is_readable($this->path);
    }

    /**
     * @return bool
     */
    public function isWritable() : bool
    {
        return is_writable($this->path);
    }

    /**
     * @param string $path
     * @param int $permissions
     * @return Directory
     * @throws ExistsException
     * @throws WriteException
     * @throws FileTypeException
     */
    public static function create(string $path, int $permissions=0755) : Directory
    {
        return DirectoryHelper::create($path, $permissions);
    }

    /**
     * Creates a directory inside of the current directory
     *
     * @param string $path
     * @param int $permissions
     * @return Directory
     * @throws ExistsException
     * @throws WriteException
     * @throws FileTypeException
     */
    public function mkdir(string $path, int $permissions=0755) : Directory
    {
        if(!$this->isWritable()){
            throw new WriteException("Directory {$this->path} is not writable!");
        }

        $path = PathHelper::createAbsolutePath($this->path, $path);
        return DirectoryHelper::create($path, $permissions);
    }

    /**
     * Returns a Tree (containing File objects and Directory objects)
     *
     * @return Tree
     * @throws ReadException
     * @throws FileTypeException
     * @throws ExistsException
     */
    public function getTree() : Tree
    {
        $this->checkIfDeleted(__METHOD__);
        return DirectoryHelper::getTree($this->path);
    }

    /**
     * @return iterable
     * @throws ReadException
     * @throws FileTypeException
     * @throws ExistsException
     */
    public function iterateTree() : iterable
    {
        $this->checkIfDeleted(__METHOD__);
        yield from DirectoryHelper::iterateTree($this->toString());
    }

    /**
     * Deletes a directory recursively
     *
     * @return Directory
     * @throws ExistsException
     * @throws FileTypeException
     * @throws ReadException
     * @throws WriteException
     */
    public function delete() : Directory
    {
        $this->checkIfDeleted(__METHOD__);

        DirectoryHelper::delete($this->path);
        $this->isDeleted = true;
        return $this;
    }

    /**
     * @param int $permissions
     * @return Directory
     * @throws WriteException
     * @throws ExistsException
     */
    public function chmod(int $permissions) : Directory
    {
        $this->checkIfDeleted(__METHOD__);

        if(!$this->isWritable()){
            throw new WriteException("Directory {$this->path} is not writable!");
        }

        PermissionsHelper::chmod($this->path, $permissions);
        return $this;
    }

    /**
     * @param string $dest
     * @return Directory
     * @throws ExistsException
     */
    public function copy(string $dest) : Directory
    {
        $this->checkIfDeleted(__METHOD__);
        return DirectoryHelper::copy($this->toString(), $dest);
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
     * @throws ExistsException
     */
    private function checkIfDeleted(string $operation) : void
    {
        if(!$this->isDeleted){
            return;
        }

        $msg = sprintf('Can not %s directory "%s", it has been deleted', $operation, $this->path);

        throw new ExistsException($msg);
    }
    //</editor-fold>

}