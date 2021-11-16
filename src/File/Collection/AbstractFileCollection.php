<?php declare(strict_types=1);

namespace LDL\File\Collection;

use LDL\File\File;
use LDL\Framework\Base\Collection\Contracts\CollectionInterface;
use LDL\Type\Collection\AbstractTypedCollection;

abstract class AbstractFileCollection extends AbstractTypedCollection
{

    public function append($item, $key = null): CollectionInterface
    {
        if(is_string($item)){
            $item = new File($item);
        }

        return parent::append($item);
    }

}
