<?php

declare(strict_types=1);

/**
 * A FileTree represents a mix of directories and files, a tree may have a root directory
 * or not.
 */

namespace LDL\File;

use LDL\File\Collection\Contracts\DirectoryCollectionInterface;
use LDL\File\Collection\Contracts\FileCollectionInterface;
use LDL\File\Collection\Contracts\LinkCollectionInterface;
use LDL\File\Collection\DirectoryCollection;
use LDL\File\Collection\FileCollection;
use LDL\File\Collection\LinkCollection;
use LDL\File\Constants\FileTypeConstants;
use LDL\File\Contracts\DirectoryInterface;
use LDL\File\Contracts\FilePermissionsReadInterface;
use LDL\File\Contracts\FileTreeInterface;
use LDL\File\Contracts\LDLFileInterface;
use LDL\File\Factory\FileFactory;
use LDL\File\Helper\DirectoryHelper;
use LDL\File\Traits\FileObserveTreeTrait;
use LDL\Framework\Base\Collection\Contracts\CollectionInterface;
use LDL\Framework\Base\Constants;
use LDL\Framework\Helper\ComparisonOperatorHelper;
use LDL\Framework\Helper\IterableHelper;
use LDL\Framework\Helper\SortHelper;
use LDL\Type\Collection\AbstractTypedCollection;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;
use LDL\Validators\Chain\OrValidatorChain;
use LDL\Validators\InterfaceComplianceValidator;

final class FileTree extends AbstractTypedCollection implements FileTreeInterface
{
    use AppendValueValidatorChainTrait;
    use FileObserveTreeTrait;

    /**
     * @var DirectoryInterface
     */
    private $root;

    public function __construct(DirectoryInterface $root, iterable $items = null)
    {
        $this->root = $root;

        $this->getAppendValueValidatorChain(OrValidatorChain::class)
            ->getChainItems()
            ->appendMany([
                new InterfaceComplianceValidator(LDLFileInterface::class, false),
            ])
            ->lock();

        $items = $items ?? DirectoryHelper::getFilesAsStringArray($root->getPath());

        parent::__construct(IterableHelper::map($items, function ($file) {
            return FileFactory::fromTree((string) $file, $this);
        }));
    }

    /**
     * @return Directory
     */
    public function getRoot(): DirectoryInterface
    {
        return $this->root;
    }

    public function append($item, $key = null): CollectionInterface
    {
        return parent::append($item, $item->getPath());
    }

    public function removeByKey(
        $key,
        string $operator = Constants::OPERATOR_SEQ,
        string $order = Constants::COMPARE_LTR
    ): int {
        if ($key !== $this->getRoot()->getPath()) {
            return parent::removeByKey($key, $operator, $order);
        }

        $count = count($this);
        $this->setItems([]);

        return $count;
    }

    public function remove(LDLFileInterface $file): FileTreeInterface
    {
        $key = $file->getPath();

        if ($key !== $this->getRoot()->getPath()) {
            $this->removeByKey($key, Constants::OPERATOR_SEQ, Constants::COMPARE_LTR);

            return $this;
        }

        $count = count($this);
        $this->setItems([]);

        return $count;
    }

    public function filterByFileType(string $type, bool $negated = false): FileTreeInterface
    {
        return $this->filterByFileTypes([$type], $negated);
    }

    public function filterByFileTypes(iterable $types, bool $negated = false): FileTreeInterface
    {
        $types = IterableHelper::toArray($types);

        /**
         * @var FileTreeInterface $result
         */
        $result = $this->filter(static function ($file) use ($types, $negated) {
            $hasType = in_array($file->getType(), $types, true);

            return $negated ? !$hasType : $hasType;
        });

        return $result;
    }

    public function filterDirectories(): DirectoryCollectionInterface
    {
        return new DirectoryCollection($this->filterByFileType(FileTypeConstants::FILE_TYPE_DIRECTORY));
    }

    public function filterFiles(): FileCollectionInterface
    {
        return new FileCollection($this->filterByFileType(FileTypeConstants::FILE_TYPE_REGULAR));
    }

    public function filterLinks(): LinkCollectionInterface
    {
        return new LinkCollection($this->filterByFileType(FileTypeConstants::FILE_TYPE_LINK));
    }

    public function filterReadable(bool $negated = false): FileTreeInterface
    {
        /**
         * @var FileTreeInterface $result
         */
        $result = $this->filter(
            static function ($file) use ($negated) {
                if (!$file instanceof FilePermissionsReadInterface) {
                    return false;
                }

                return $negated ? !$file->isReadable() : $file->isReadable();
            });

        return $result;
    }

    public function filterWritable(bool $negated = false): FileTreeInterface
    {
        /**
         * @var FileTreeInterface $result
         */
        $result = $this->filter(
            static function ($file) use ($negated) {
                if (!$file instanceof FilePermissionsReadInterface) {
                    return false;
                }

                return $negated ? !$file->isWritable() : $file->isWritable();
            });

        return $result;
    }

    public function filterReadWrite(bool $negated = false): FileTreeInterface
    {
        /**
         * @var FileTreeInterface $result
         */
        $result = $this->filter(
            static function ($file) use ($negated) {
                if (!$file instanceof FilePermissionsReadInterface) {
                    return false;
                }

                $isReadWrite = $file->isReadable() && $file->isWritable();

                return $negated ? !$isReadWrite : $isReadWrite;
            });

        return $result;
    }

    public function filterByPermissions(iterable $permissions, bool $negated = false): FileTreeInterface
    {
        $permissions = IterableHelper::toArray($permissions);
        /**
         * @var FileTreeInterface $result
         */
        $result = $this->filter(
            static function ($file) use ($permissions, $negated) {
                if (!$file instanceof FilePermissionsReadInterface) {
                    return false;
                }

                $hasPermissions = in_array($file->getPerms(), $permissions, true);

                return $negated ? !$hasPermissions : $hasPermissions;
            });

        return $result;
    }

    public function traverse(): iterable
    {
        /**
         * @var LDLFileInterface $file
         */
        foreach ($this as $file) {
            if ($file instanceof DirectoryInterface) {
                yield from $file->getTree();
            } else {
                yield $file;
            }
        }
    }

    public function sortByDateCreated(string $order = Constants::SORT_ASCENDING): FileTreeInterface
    {
        $operator = Constants::SORT_DESCENDING ? Constants::OPERATOR_GT : Constants::OPERATOR_LT;
        $direction = Constants::SORT_DESCENDING ? Constants::COMPARE_LTR : Constants::COMPARE_RTL;

        return new self(
            $this->root,
            SortHelper::sortByCallback(
                static function (LDLFileInterface $fileA, LDLFileInterface $fileB) use ($operator, $direction): bool {
                    return ComparisonOperatorHelper::compare(
                        $fileA->getDateCreated()->getTimestamp(),
                        $fileB->getDateCreated()->getTimestamp(),
                        $operator,
                        $direction
                    );
                }, iterator_to_array($this, true)
            )
        );
    }

    public function sortByDateAccessed(string $order = Constants::SORT_ASCENDING): FileTreeInterface
    {
        $operator = Constants::SORT_DESCENDING ? Constants::OPERATOR_GT : Constants::OPERATOR_LT;
        $direction = Constants::SORT_DESCENDING ? Constants::COMPARE_LTR : Constants::COMPARE_RTL;

        return new self(
            $this->root,
            SortHelper::sortByCallback(
                static function (LDLFileInterface $fileA, LDLFileInterface $fileB) use ($operator, $direction): bool {
                    return ComparisonOperatorHelper::compare(
                        $fileA->getDateAccessed()->getTimestamp(),
                        $fileB->getDateAccessed()->getTimestamp(),
                        $operator,
                        $direction
                    );
                }, iterator_to_array($this, true)
            )
        );
    }

    public function refresh(): FileTreeInterface
    {
        $this->setItems(DirectoryHelper::getTree($this->root->toString()));

        return $this;
    }
}
