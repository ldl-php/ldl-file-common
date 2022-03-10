<?php

declare(strict_types=1);

namespace LDL\File\Collection\Contracts;

use LDL\File\Exception\FileExistsException;
use LDL\Framework\Base\Contracts\ArrayFactoryInterface;
use LDL\Framework\Base\Contracts\IterableFactoryInterface;
use LDL\Type\Collection\TypedCollectionInterface;

/**
 * Interface DirectoryCollectionInterface.
 */

interface DirectoryCollectionInterface extends TypedCollectionInterface, IterableFactoryInterface, ArrayFactoryInterface
{
    /**
     * Yields each directory's real path in the collection.
     *
     * @throws FileExistsException
     */
    public function getRealPaths(): iterable;
}
