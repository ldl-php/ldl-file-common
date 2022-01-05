<?php

declare(strict_types=1);

namespace LDL\File\Collection;

use LDL\File\Collection\Contracts\LinkCollectionInterface;
use LDL\File\Collection\Traits\AppendFileAsStringTrait;
use LDL\File\Collection\Traits\FileCollectionFactoryTrait;
use LDL\File\Contracts\LinkInterface;
use LDL\Type\Collection\AbstractTypedCollection;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;
use LDL\Validators\InterfaceComplianceValidator;

final class LinkCollection extends AbstractTypedCollection implements LinkCollectionInterface
{
    use AppendValueValidatorChainTrait;
    use AppendFileAsStringTrait;
    use FileCollectionFactoryTrait;

    public function __construct(iterable $items = null)
    {
        $this->getAppendValueValidatorChain()
            ->getChainItems()
            ->appendMany([
                new InterfaceComplianceValidator(LinkInterface::class),
            ])
            ->lock();

        parent::__construct($items);
    }
}
