<?php declare(strict_types=1);

namespace LDL\File\Collection;

use LDL\File\Constants\FileTypeConstants;
use LDL\File\Validator\FileTypeValidator;
use LDL\Type\Collection\AbstractTypedCollection;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;

final class DirectoryCollection extends AbstractTypedCollection
{
    use AppendValueValidatorChainTrait;

    public function __construct(iterable $items = null)
    {
        parent::__construct($items);

        $this->getAppendValueValidatorChain()
            ->getChainItems()
            ->append(new FileTypeValidator([FileTypeConstants::FILE_TYPE_DIRECTORY]))
            ->lock();
    }
}
