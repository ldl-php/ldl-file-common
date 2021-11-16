<?php declare(strict_types=1);

namespace LDL\File\Collection;

use LDL\File\File;
use LDL\File\Validator\FileExistsValidator;
use LDL\File\Validator\ReadableFileValidator;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;
use LDL\Validators\ClassComplianceValidator;

final class ReadableFileCollection extends AbstractFileCollection
{
    use AppendValueValidatorChainTrait;

    public function __construct(iterable $items = null)
    {
        $this->getAppendValueValidatorChain()
            ->getChainItems()
            ->appendMany([
                new ClassComplianceValidator(File::class,true),
                new FileExistsValidator(),
                new ReadableFileValidator()
            ])
            ->lock();

        parent::__construct($items);
    }
}