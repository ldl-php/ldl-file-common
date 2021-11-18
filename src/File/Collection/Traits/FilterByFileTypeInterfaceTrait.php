<?php declare(strict_types=1);

namespace LDL\File\Collection\Traits;

use LDL\File\Collection\Contracts\FilterByFileTypeInterface;
use LDL\File\Directory;
use LDL\File\File;
use LDL\File\FileTree;
use LDL\Framework\Helper\ClassHelper;
use LDL\Framework\Helper\IterableHelper;

trait FilterByFileTypeInterfaceTrait
{

    /**
     * @param string $type
     * @return FileTree
     */
    public function filterByFileType(string $type, string $root=null) : FileTree
    {
        return $this->filterByFileTypes([$type], $root);
    }

    public function filterByFileTypes(iterable $types, string $root=null) : FileTree
    {
        ClassHelper::mustHaveInterface(get_class($this), FilterByFileTypeInterface::class);
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

        return new FileTree(null, $filtered);
    }

}