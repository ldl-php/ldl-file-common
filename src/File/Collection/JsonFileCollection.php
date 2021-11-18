<?php declare(strict_types=1);

namespace LDL\File\Collection;

use LDL\File\Collection\Contracts\FileCollectionInterface;
use LDL\File\Collection\Traits\AppendFileAsStringTrait;
use LDL\File\Collection\Traits\ReadFileLinesInterfaceTrait;
use LDL\File\Contracts\FileInterface;
use LDL\File\Contracts\ReadFileLinesInterface;
use LDL\File\Validator\JsonFileValidator;
use LDL\File\Validator\ReadableFileValidator;
use LDL\Type\Collection\AbstractTypedCollection;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;
use LDL\Validators\InterfaceComplianceValidator;

final class JsonFileCollection extends AbstractTypedCollection implements FileCollectionInterface, ReadFileLinesInterface
{
    use AppendValueValidatorChainTrait;
    use AppendFileAsStringTrait;
    use ReadFileLinesInterfaceTrait;

    public function __construct(iterable $items = null)
    {
        $this->getAppendValueValidatorChain()
            ->getChainItems()
            ->appendMany([
                new InterfaceComplianceValidator(FileInterface::class),
                new ReadableFileValidator(),
                new JsonFileValidator()
            ])
            ->lock();

        parent::__construct($items);
    }
}
