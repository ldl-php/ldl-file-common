<?php declare(strict_types=1);

/**
 * A FileTree represents a mix of directories and files, a tree may have a root directory
 * or not.
 */

namespace LDL\File;

use LDL\File\Collection\DirectoryCollection;
use LDL\File\Collection\FileCollection;
use LDL\File\Constants\FileTypeConstants;
use LDL\File\Contracts\DirectoryInterface;
use LDL\File\Contracts\FileInterface;
use LDL\File\Exception\FileWriteException;
use LDL\File\Helper\DirectoryHelper;
use LDL\Framework\Base\Collection\Contracts\FilterByClassInterface;
use LDL\Framework\Base\Collection\Traits\FilterByClassInterfaceTrait;
use LDL\Framework\Helper\IterableHelper;
use LDL\Type\Collection\AbstractTypedCollection;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;
use LDL\Validators\Chain\OrValidatorChain;
use LDL\Validators\ClassComplianceValidator;
use LDL\Validators\InterfaceComplianceValidator;

final class FileTree extends AbstractTypedCollection implements FilterByClassInterface
{
    use AppendValueValidatorChainTrait;
    use FilterByClassInterfaceTrait;

    /**
     * @var Directory
     */
    private $root;

    public function __construct(Directory $root, iterable $items = null)
    {
        $this->root = $root;

        $this->getAppendValueValidatorChain(OrValidatorChain::class)
            ->getChainItems()
            ->appendMany([
                new InterfaceComplianceValidator(FileInterface::class,false),
                new InterfaceComplianceValidator(DirectoryInterface::class,false)
            ])
            ->lock();

        parent::__construct($items);
    }

    /**
     * @return Directory
     */
    public function getRoot() : Directory
    {
        return $this->root;
    }

    /**
     * Deletes a tree
     *
     * @return FileTree
     * @throws Exception\FileExistsException
     * @throws Exception\FileTypeException
     * @throws Exception\FileReadException
     * @throws FileWriteException
     */
    public function delete() : FileTree
    {
        /**
         * @var File|Directory $file
         */
        foreach($this as $file){
            $file->delete();
        }

        return $this;
    }

    /**
     * Obtains all files in the current tree, syntax sugar for filterByClass(File::class)
     *
     * @return FileCollection
     */
    public function filterFiles() : FileCollection
    {
        return new FileCollection($this->filterByClass(File::class));
    }

    /**
     * Obtains all directories in the current tree, syntax sugar for filterByClass(Directory::class)
     * @return DirectoryCollection
     */
    public function filterDirectories() : DirectoryCollection
    {
        return new DirectoryCollection($this->filterByClass(Directory::class));
    }

    /**
     * Returns a new FileTree instance with files filtered by type
     *
     * NOTE: A new file tree is returned since we can filter by files or directories
     *
     * @see FileTypeConstants
     *
     * @param string $type
     * @return FileTree
     * @throws Exception\FileExistsException
     * @throws Exception\FileReadException
     */
    public function filterByFileType(string $type) : FileTree
    {
        return $this->filterByFileTypes([$type]);
    }

    /**
     * Returns a new FileTree instance with files filtered by types
     *
     * NOTE: A new file tree is returned since we can filter by files or directories
     *
     * @see FileTypeConstants
     *
     * @param iterable $types
     * @return FileTree
     * @throws Exception\FileExistsException
     * @throws Exception\FileReadException
     */
    public function filterByFileTypes(iterable $types) : FileTree
    {
        $types = IterableHelper::toArray($types);

        $filtered = [];

        /**
         * @var File|Directory $file
         */
        foreach($this as $file){
            if(in_array($file->getType(), $types, true)){
                $filtered[] = $file;
            }
        }

        return new self($this->root, $filtered);
    }

    /**
     * Filters readable files and directories and returns a FileTree containing only readable files
     *
     * @return FileTree
     */
    public function filterReadable() : FileTree
    {
        $return = new self($this->root);

        /**
         * @var File|Directory $file
         */
        foreach($this as $file){
            if($file->isReadable()){
                $return->append($file);
            }
        }

        return $return;
    }

    /**
     * Filters writable files and directories and returns a FileTree containing only writable files
     *
     * @return FileTree
     */
    public function filterWritable() : FileTree
    {
        $return = new self($this->root);

        /**
         * @var File|Directory $file
         */
        foreach($this as $file){
            if($file->isWritable()){
                $return->append($file);
            }
        }

        return $return;
    }

    /**
     * Filters read & writable files and directories and returns a FileTree containing only read & writable files
     * @return FileTree
     */
    public function filterReadWrite() : FileTree
    {
        $return = new self($this->root);

        /**
         * @var File|Directory $file
         */
        foreach($this as $file){
            if($file->isReadable() && $file->isWritable()){
                $return->append($file);
            }
        }

        return $return;
    }

    public function traverse() : iterable
    {
        /**
         * @var File|Directory $file
         */
        foreach($this as $file){
            if($file instanceof File){
                yield $file;
            }else{
                yield from $file->getTree();
            }
        }
    }

    /**
     * Change permissions of all files included in this tree to $permissions
     *
     * @param int $permissions
     * @throws Exception\FileExistsException
     * @throws FileWriteException
     */
    public function chmod(int $permissions) : void
    {
        /**
         * @var File|Directory $file
         */
        foreach($this as $file){
           $file->chmod($permissions);
        }
    }

    /**
     * This method allows you to refresh the tree (re scan the root directory)
     * It is useful when files have been added or removed from the root directory.
     *
     * @return FileTree
     * @throws Exception\FileExistsException
     * @throws Exception\FileTypeException
     * @throws Exception\FileReadException
     */
    public function refresh() : FileTree
    {
        $this->setItems(DirectoryHelper::getTree($this->root->toString()));
        return $this;
    }
}