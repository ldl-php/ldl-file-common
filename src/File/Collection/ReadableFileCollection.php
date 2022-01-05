<?php

declare(strict_types=1);

namespace LDL\File\Collection;

use LDL\File\Collection\Contracts\FileCollectionInterface;
use LDL\File\Collection\Traits\AppendFileAsStringTrait;
use LDL\File\Collection\Traits\FileCollectionFactoryTrait;
use LDL\File\Collection\Traits\ReadFileLinesInterfaceTrait;
use LDL\File\Contracts\FileInterface;
use LDL\File\Contracts\LDLFileInterface;
use LDL\File\Contracts\ReadFileLinesInterface;
use LDL\File\Validator\FileExistsValidator;
use LDL\File\Validator\ReadableFileValidator;
use LDL\Type\Collection\AbstractTypedCollection;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;
use LDL\Type\Collection\Types\String\StringCollection;
use LDL\Validators\InterfaceComplianceValidator;

final class ReadableFileCollection extends AbstractTypedCollection implements FileCollectionInterface, ReadFileLinesInterface
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
                new InterfaceComplianceValidator(LDLFileInterface::class),
                new FileExistsValidator(),
                new ReadableFileValidator(),
            ])
            ->lock();

        parent::__construct($items);
    }

    public function getLinesAsString(?string $separator): string
    {
        $return = new StringCollection();

        /**
         * @var FileInterface $file
         */
        foreach ($this as $file) {
            $return->appendMany($file->getLines());
        }

        return $return->implode($separator);
    }
}
