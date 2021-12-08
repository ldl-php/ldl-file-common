<?php declare(strict_types=1);

namespace LDL\File\Collection\Traits;

use LDL\File\Collection\Contracts\FileCollectionInterface;
use LDL\File\Contracts\FileInterface;
use LDL\File\File;
use LDL\Framework\Base\Exception\ArrayFactoryException;
use LDL\Framework\Base\Exception\IterableFactoryException;
use LDL\Framework\Helper\IterableHelper;
use LDL\Type\Collection\Types\String\StringCollection;

trait FileCollectionFactoryTrait
{

    public static function fromArray(array $files = []): FileCollectionInterface
    {
        $data = new StringCollection($files);

        $items = new self();
        foreach($data as $key => $file){
            try {
                $items->append($file instanceof FileInterface ? $file : new File((string)$file), $key);
            } catch (\Throwable $e) {
                throw new ArrayFactoryException($e->getMessage(), $e->getCode(), $e);
            }
        }

        return new self($items);
    }

    /**
     * @param iterable $items
     * @return FileCollectionInterface
     * @throws IterableFactoryException
     */
    public static function fromIterable(iterable $items): FileCollectionInterface
    {
        try {
            return self::fromArray(IterableHelper::toArray($items));
        }catch(ArrayFactoryException $e){
            throw new IterableFactoryException($e->getMessage(),$e->getCode(), $e);
        }
    }
}
