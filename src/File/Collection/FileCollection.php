<?php declare(strict_types=1);

namespace LDL\File\Collection;

use LDL\File\Directory;
use LDL\File\File;
use LDL\Framework\Base\Collection\Contracts\CollectionInterface;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;
use LDL\Validators\Chain\OrValidatorChain;
use LDL\Validators\ClassComplianceValidator;

final class FileCollection extends AbstractFileCollection
{
    use AppendValueValidatorChainTrait;

    public function __construct(iterable $items = null)
    {
        $this->getAppendValueValidatorChain(OrValidatorChain::class)
            ->getChainItems()
            ->appendMany([
                new ClassComplianceValidator(File::class,true),
                new ClassComplianceValidator(Directory::class,true)
            ])
            ->lock();

        parent::__construct($items);
    }

    public function append($item, $key = null): CollectionInterface
    {
        if(is_string($item)){
            $item = new File($item);
        }

        return parent::append($item, $key);
    }
}