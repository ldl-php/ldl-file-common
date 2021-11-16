<?php declare(strict_types=1);

namespace LDL\File\Collection;

use LDL\File\File;
use LDL\File\Validator\JsonFileValidator;
use LDL\File\Validator\ReadableFileValidator;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;
use LDL\Validators\ClassComplianceValidator;

final class JsonFileCollection extends AbstractFileCollection
{
    use AppendValueValidatorChainTrait;

    public function __construct(iterable $items = null)
    {
        $this->getAppendValueValidatorChain()
            ->getChainItems()
            ->appendMany([
                new ClassComplianceValidator(File::class, true),
                new ReadableFileValidator(),
                new JsonFileValidator()
            ])
            ->lock();

        parent::__construct($items);
    }
}
