<?php

declare(strict_types=1);

namespace LDL\File;

use LDL\File\Constants\FileTypeConstants;
use LDL\File\Contracts\DirectoryInterface;
use LDL\File\Contracts\FileInterface;
use LDL\File\Contracts\FileTreeInterface;
use LDL\File\Contracts\LDLFileInterface;
use LDL\File\Contracts\LinkInterface;
use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileTypeException;
use LDL\File\Exception\FileWriteException;
use LDL\File\Helper\DirectoryHelper;
use LDL\File\Helper\FilePathHelper;
use LDL\File\Helper\FilePermsHelper;
use LDL\File\Helper\LinkHelper;
use LDL\File\Traits\FileDateTrait;
use LDL\File\Traits\FileNameTrait;
use LDL\File\Traits\FileObserveTreeTrait;
use LDL\File\Traits\FileOwnershipTrait;

final class Directory implements DirectoryInterface
{
    use FileDateTrait;
    use FileObserveTreeTrait;
    use FileOwnershipTrait;
    use FileNameTrait;

    /**
     * @var string
     */
    private $path;

    /**
     * @var bool
     */
    private $isDeleted = false;

    /**
     * Directory constructor.
     *
     * @throws FileTypeException
     */
    public function __construct(string $path, FileTreeInterface $tree = null)
    {
        if (is_dir($path)) {
            $this->path = $path;

            return;
        }

        $this->_tObserveTreeTrait = $tree;

        throw new FileTypeException(sprintf('File %s does not exists or is not a directory, if you wish to create it, use %s::create', $path, __CLASS__));
    }

    public function getDirectory(int $levels = null): DirectoryInterface
    {
        $levels = $levels ?? 1;
        $this->checkIfDeleted(__METHOD__);

        return new Directory(dirname($this->path, $levels), $this->_tObserveTreeTrait);
    }

    public static function create(string $path, int $permissions = 0755, bool $force = false): DirectoryInterface
    {
        return DirectoryHelper::create($path, $permissions, $force);
    }

    public function getType(): string
    {
        return FileTypeConstants::FILE_TYPE_DIRECTORY;
    }

    public function mkdir(string $path, int $permissions = 0755, bool $overwrite = false): DirectoryInterface
    {
        if (!$this->isWritable()) {
            throw new FileWriteException("Directory {$this->path} is not writable!");
        }

        $path = FilePathHelper::createAbsolutePath($this->path, $path);

        return DirectoryHelper::create($path, $permissions, $overwrite);
    }

    public function mkfile(
        string $name,
        string $contents = '',
        int $permissions = 0644,
        bool $overwrite = false
    ): FileInterface {
        $name = basename($name);

        return File::create(
            FilePathHelper::createAbsolutePath($this->path, $name),
            $contents,
            $permissions,
            $overwrite
        );
    }

    /**
     * @TODO Needs improvement, this should not re-scan all files the instance of _tObserveTreeTrait should remain
     * for maximum performance.
     * {@inheritdoc}
     */
    public function getTree(bool $observable = true): FileTreeInterface
    {
        $this->_tObserveTreeTrait = DirectoryHelper::getTree($this->path, $observable);

        return $this->_tObserveTreeTrait;
    }

    public function chown(?string $user, ?string $group, bool $recursive = false): void
    {
        DirectoryHelper::chown($this->path, $user, $group, $recursive);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(): void
    {
        $this->checkIfDeleted(__METHOD__);
        DirectoryHelper::delete($this->path);
        $this->_tObserveTreeTraitRefreshTree();
        $this->isDeleted = true;
    }

    public function chmod(int $permissions, bool $recursive = false): LDLFileInterface
    {
        $this->checkIfDeleted(__METHOD__);

        FilePermsHelper::chmod($this->path, $permissions, $recursive);

        return $this;
    }

    public function isReadable(): bool
    {
        return is_readable($this->path);
    }

    public function isWritable(): bool
    {
        return is_writable($this->path);
    }

    public function getPerms(): int
    {
        $this->checkIfDeleted(__METHOD__);

        return FilePermsHelper::getPerms($this->toString());
    }

    public function copy(string $dest, bool $force = false): LDLFileInterface
    {
        $this->checkIfDeleted(__METHOD__);

        return DirectoryHelper::copy($this->toString(), $dest, $force);
    }

    public function move(string $dest, bool $force = false): LDLFileInterface
    {
        $this->checkIfDeleted(__METHOD__);
        $return = DirectoryHelper::move($this->path, $dest, $force);
        $this->isDeleted = true;

        return $return;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function link(string $target, bool $force = false): LinkInterface
    {
        return LinkHelper::create($this->path, $target, $force);
    }

    public function getLinkTarget(): DirectoryInterface
    {
        return LinkHelper::getTarget($this->path);
    }

    public function mkpath(...$pieces): string
    {
        return FilePathHelper::createAbsolutePath($this->getPath(), ...$pieces);
    }

    //<editor-fold desc="ToStringInterface Methods">
    public function toString(): string
    {
        return $this->path;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
    //</editor-fold>

    //<editor-fold desc="Private methods">
    /**
     * @throws FileExistsException
     */
    private function checkIfDeleted(string $operation): void
    {
        if (!$this->isDeleted) {
            return;
        }

        $msg = sprintf('Can not %s directory "%s", it has been deleted', $operation, $this->path);

        throw new FileExistsException($msg);
    }
    //</editor-fold>
}
