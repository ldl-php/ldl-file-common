<?php declare(strict_types=1);

namespace LDL\File\Collection;

use LDL\File\Collection\Contracts\FileCollectionInterface;
use LDL\File\Collection\Traits\FileCollectionFactoryTrait;
use LDL\File\Collection\Traits\ReadFileLinesInterfaceTrait;
use LDL\File\Contracts\FileInterface;
use LDL\File\Contracts\ReadFileLinesInterface;
use LDL\File\File;
use LDL\File\Validator\FileExistsValidator;
use LDL\File\Validator\ReadableFileValidator;
use LDL\File\Validator\WritableFileValidator;
use LDL\Type\Collection\AbstractTypedCollection;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;
use LDL\File\Collection\Traits\AppendFileAsStringTrait;
use LDL\Validators\InterfaceComplianceValidator;

final class ReadWriteFileCollection extends AbstractTypedCollection implements FileCollectionInterface, ReadFileLinesInterface
{
    use AppendValueValidatorChainTrait;
    use AppendFileAsStringTrait;
    use ReadFileLinesInterfaceTrait;
    use FileCollectionFactoryTrait;

    public function __construct(iterable $items = null)
    {
        $this->getAppendValueValidatorChain()
            ->getChainItems()
            ->appendMany([
                new InterfaceComplianceValidator(FileInterface::class),
                new FileExistsValidator(),
                new ReadableFileValidator(),
                new WritableFileValidator()
            ])
            ->lock();

        parent::__construct($items);
    }

}