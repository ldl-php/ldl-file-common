<?php declare(strict_types=1);

namespace LDL\File;

use LDL\Type\Collection\AbstractTypedCollection;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;
use LDL\Validators\Chain\OrValidatorChain;
use LDL\Validators\ClassComplianceValidator;

final class Tree extends AbstractTypedCollection
{
    use AppendValueValidatorChainTrait;

    private $root;

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

    /**
     * Deletes a tree
     *
     * @return Tree
     */
    public function delete() : Tree
    {
        /**
         * @var File|Directory $file
         */
        foreach($this as $file){
            $file->delete();
        }

        return $this;
    }

}