<?php declare(strict_types=1);

namespace LDL\File\Collection\Traits;

use LDL\File\Directory;
use LDL\Framework\Base\Collection\Contracts\CollectionInterface;

trait AppendDirectoryAsStringTrait
{

    /**
     * @param $item
     * @param null $key
     * @return CollectionInterface
     * @throws \LDL\File\Exception\FileTypeException
     */
    public function append($item, $key = null): CollectionInterface
    {
        return parent::append(is_string($item) ? new Directory($item) : $item, $key);
    }

}
