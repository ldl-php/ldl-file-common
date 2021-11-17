<?php declare(strict_types=1);

namespace LDL\File\Collection\Traits;

use LDL\File\File;
use LDL\Framework\Base\Collection\Contracts\CollectionInterface;

trait AppendFileAsStringTrait
{

    /**
     * @param $item
     * @param null $key
     * @return CollectionInterface
     * @throws \LDL\File\Exception\ExistsException
     * @throws \LDL\File\Exception\FileTypeException
     */
    public function append($item, $key = null): CollectionInterface
    {
        return parent::append(is_string($item) ? new File($item) : $item, $key);
    }

}
