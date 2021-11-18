<?php declare(strict_types=1);

namespace LDL\File\Collection;

use LDL\File\Collection\Contracts\FileCollectionInterface;
use LDL\File\Collection\Traits\AppendFileAsStringTrait;
use LDL\File\Contracts\FileInterface;
use LDL\File\Validator\FileExistsValidator;
use LDL\File\Validator\ReadableFileValidator;
use LDL\Type\Collection\AbstractTypedCollection;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;
use LDL\Validators\InterfaceComplianceValidator;

final class ReadableFileCollection extends AbstractTypedCollection implements FileCollectionInterface
{
    use AppendValueValidatorChainTrait;
    use AppendFileAsStringTrait;

    public function __construct(iterable $items = null)
    {
        $this->getAppendValueValidatorChain()
            ->getChainItems()
            ->appendMany([
                new InterfaceComplianceValidator(FileInterface::class),
                new FileExistsValidator(),
                new ReadableFileValidator()
            ])
            ->lock();

        parent::__construct($items);
    }

}