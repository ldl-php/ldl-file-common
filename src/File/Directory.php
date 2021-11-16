<?php declare(strict_types=1);

namespace LDL\File;

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

    public function __construct(string $path)
    {
        if(is_dir($path)){
            $this->path = $path;
            return;
        }

        throw new \InvalidArgumentException(
            sprintf(
                'File %s does not exists or is a directory, if you wish to create it, use %s::create',
                $path,
                __CLASS__
            )
        );
    }

    /**
     * @param string $path
     * @param int $permissions
     * @return Directory
     * @throws \Exception
     */
    public static function create(string $path, int $permissions=0755) : Directory
    {
        DirectoryHelper::create($path, $permissions);
        return new self($path);
    }

    /**
     * Creates a directory inside of the current directory
     *
     * @param string $path
     * @param int $permissions
     * @return Directory
     * @throws \Exception
     */
    public function mkdir(string $path, int $permissions=0755) : Directory
    {
        $path = PathHelper::createAbsolutePath($this->path, $path);
        DirectoryHelper::create($path, $permissions);
        return new self($path);
    }

    /**
     * Returns a Tree (containing File objects and Directory objects)
     * @return Tree
     */
    public function getTree() : Tree
    {
        $this->checkIfDeleted(__METHOD__);
        $tree = new Tree();
        foreach(scandir($this->path) as $file){
            $file = PathHelper::createAbsolutePath($this->path, $file);
            $tree->append(is_dir($file) ? new self($file) : new File($file));
        }

        return $tree;
    }

    /**
     * @return Tree
     */
    public function iterateTree() : iterable
    {
        $this->checkIfDeleted(__METHOD__);

        foreach(scandir($this->path) as $file){
            yield is_dir($file) ? new self($file) : new File($file);
        }
    }

    /**
     * Deletes a directory recursively
     *
     * @return Directory
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
     */
    public function chmod(int $permissions) : Directory
    {
        $this->checkIfDeleted(__METHOD__);
        PermissionsHelper::chmod($this->path, $permissions);
        return $this;
    }

    /**
     * @param string $dest
     * @return Directory
     */
    public function copy(string $dest) : Directory
    {
        throw new \LogicException("@TODO! Copy recursively to $dest directory");
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
     * @throws \RuntimeException
     */
    private function checkIfDeleted(string $operation) : void
    {
        if(!$this->isDeleted){
            return;
        }

        $msg = sprintf('Can not %s directory "%s", it has been deleted', $operation, $this->path);

        throw new \RuntimeException($msg);
    }
    //</editor-fold>

}