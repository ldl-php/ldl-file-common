<?php

declare(strict_types=1);

namespace LDL\File\Contracts;

use LDL\File\Collection\Contracts\DirectoryCollectionInterface;
use LDL\File\Collection\Contracts\FileCollectionInterface;
use LDL\File\Collection\Contracts\LinkCollectionInterface;
use LDL\File\Constants\FileTypeConstants;
use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileReadException;
use LDL\File\Exception\FileTypeException;
use LDL\Type\Collection\TypedCollectionInterface;

interface FileTreeInterface extends TypedCollectionInterface
{
    /**
     * Obtains the root directory.
     */
    public function getRoot(): DirectoryInterface;

    /**
     * This method allows you to refresh the tree (re scan the root directory)
     * It is useful when files have been added or removed from the root directory.
     *
     * @throws FileExistsException
     * @throws FileTypeException
     * @throws FileReadException
     */
    public function refresh(): FileTreeInterface;

    /**
     * Remove a file from the tree, DO NOT confuse this as in "Deleting a file"
     * this will just remove a file object out of the tree.
     */
    public function remove(LDLFileInterface $file): FileTreeInterface;

    /**
     * Traverse file tree by using generators.
     *
     * This is the equivalent of recursively iterating through all files and directories in the tree.
     */
    public function traverse(): iterable;

    /**
     * Filter writable files.
     */
    public function filterWritable(bool $negated): FileTreeInterface;

    /**
     * Filter writable files.
     */
    public function filterReadable(bool $negated): FileTreeInterface;

    /**
     * Filters tree by a single file type.
     *
     * @see FileTypeConstants for available file types
     */
    public function filterByFileType(string $type, bool $negated): FileTreeInterface;

    /**
     * Filters tree by file types.
     *
     * @see FileTypeConstants for available file types
     */
    public function filterByFileTypes(iterable $types, bool $negated): FileTreeInterface;

    /**
     * Filter files and return a FileCollection.
     */
    public function filterFiles(): FileCollectionInterface;

    /**
     * Filter links and return a LinkCollection.
     */
    public function filterLinks(): LinkCollectionInterface;

    /**
     * Filter directories and return a DirectoryCollection.
     */
    public function filterDirectories(): DirectoryCollectionInterface;

    public function sortByDateCreated(string $order): FileTreeInterface;

    public function sortByDateAccessed(string $order): FileTreeInterface;

    /**
     * Filters files by permissions, do not confuse this with filterReadable/Writable
     * This will filter by very specific permissions, such as 0644 or 0666.
     *
     * @param iterable $permissions an iterable containing a set of numeric permissions
     */
    public function filterByPermissions(iterable $permissions): FileTreeInterface;
}
