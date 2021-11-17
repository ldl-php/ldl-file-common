<?php declare(strict_types=1);

namespace LDL\File;

use LDL\File\Exception\WriteException;
use LDL\Type\Collection\AbstractTypedCollection;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;
use LDL\Validators\Chain\OrValidatorChain;
use LDL\Validators\ClassComplianceValidator;

final class Tree extends AbstractTypedCollection
{
    use AppendValueValidatorChainTrait;

    /**
     * @var Directory
     */
    private $root;

    public function __construct(Directory $root, iterable $items = null)
    {
        $this->root = $root;

        $this->getAppendValueValidatorChain(OrValidatorChain::class)
            ->getChainItems()
            ->appendMany([
                new ClassComplianceValidator(File::class,true),
                new ClassComplianceValidator(Directory::class,true)
            ])
            ->lock();

        parent::__construct($items);
    }

    public function getRoot() : Directory
    {
        return $this->root;
    }

    /**
     * Deletes a tree
     *
     * @return Tree
     * @throws WriteException
     */
    public function delete() : Tree
    {
        $this->root->delete();
        return $this;
    }

}