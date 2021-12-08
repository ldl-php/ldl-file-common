<?php declare(strict_types=1);

namespace LDL\File\Collection;

use LDL\File\Collection\Contracts\DirectoryCollectionInterface;
use LDL\File\Collection\Traits\AppendDirectoryAsStringTrait;
use LDL\File\Contracts\DirectoryInterface;
use LDL\File\Directory;
use LDL\Framework\Base\Exception\ArrayFactoryException;
use LDL\Framework\Base\Exception\IterableFactoryException;
use LDL\Framework\Helper\IterableHelper;
use LDL\Type\Collection\AbstractTypedCollection;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;
use LDL\Type\Collection\Types\String\StringCollection;
use LDL\Validators\InterfaceComplianceValidator;

final class DirectoryCollection extends AbstractTypedCollection implements DirectoryCollectionInterface
{
    use AppendValueValidatorChainTrait;
    use AppendDirectoryAsStringTrait;

    public function __construct(iterable $items = null)
    {
        $this->getAppendValueValidatorChain()
            ->getChainItems()
            ->append(new InterfaceComplianceValidator(DirectoryInterface::class))
            ->lock();

        parent::__construct($items);
    }

    public static function fromArray(array $directories = []): DirectoryCollectionInterface
    {
        $data = new StringCollection($directories);

        $items = new self();
        foreach($data as $key => $directory){
            try {
                $items->append($directory instanceof DirectoryInterface ? $directory : new Directory((string)$directory), $key);
            } catch (\Throwable $e) {
                throw new ArrayFactoryException($e->getMessage(), $e->getCode(), $e);
            }
        }

        return new self($items);
    }

    /**
     * @param iterable $items
     * @return DirectoryCollectionInterface
     * @throws IterableFactoryException
     */
    public static function fromIterable(iterable $items): DirectoryCollectionInterface
    {
        try {
            return self::fromArray(IterableHelper::toArray($items));
        }catch(ArrayFactoryException $e){
            throw new IterableFactoryException($e->getMessage(),$e->getCode(), $e);
        }
    }
}
