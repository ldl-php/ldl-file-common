<?php declare(strict_types=1);

namespace LDL\File\Collection;

use LDL\File\File;
use LDL\File\Validator\FileExistsValidator;
use LDL\File\Validator\WritableFileValidator;
use LDL\Type\Collection\AbstractTypedCollection;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;
use LDL\Validators\ClassComplianceValidator;

final class WritableFileCollection extends AbstractTypedCollection
{
    use AppendValueValidatorChainTrait;

    public function __construct(iterable $items = null)
    {
        $this->getAppendValueValidatorChain()
            ->getChainItems()
            ->appendMany([
                new ClassComplianceValidator(File::class, true),
                new FileExistsValidator(),
                new WritableFileValidator()
            ])
            ->lock();

        parent::__construct($items);
    }
}