<?php declare(strict_types=1);

namespace LDL\File\Collection;

use LDL\File\Collection\Contracts\DirectoryCollectionInterface;
use LDL\File\Collection\Traits\AppendDirectoryAsStringTrait;
use LDL\File\Directory;
use LDL\Type\Collection\AbstractTypedCollection;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;
use LDL\Validators\ClassComplianceValidator;

final class DirectoryCollection extends AbstractTypedCollection implements DirectoryCollectionInterface
{
    use AppendValueValidatorChainTrait;
    use AppendDirectoryAsStringTrait;

    public function __construct(iterable $items = null)
    {
        parent::__construct($items);

        $this->getAppendValueValidatorChain()
            ->getChainItems()
            ->append(new ClassComplianceValidator(Directory::class,true))
            ->lock();
    }

}
