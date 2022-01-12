<?php

declare(strict_types=1);

namespace LDL\File;

use LDL\File\Contracts\DirectoryInterface;
use LDL\File\Contracts\FileInterface;
use LDL\File\Contracts\FileTreeInterface;
use LDL\File\Contracts\LDLFileInterface;
use LDL\File\Contracts\LinkInterface;
use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileTypeException;
use LDL\File\Exception\FileWriteException;
use LDL\File\Helper\FileHelper;
use LDL\File\Helper\FilePermsHelper;
use LDL\File\Traits\FileDateTrait;
use LDL\File\Traits\FileObserveTreeTrait;
use LDL\File\Traits\FileOwnershipTrait;
use LDL\Type\Collection\Types\String\StringCollection;

final class File implements FileInterface
{
    use FileDateTrait;
    use FileObserveTreeTrait;
    use FileOwnershipTrait;

    /**
     * @var string
     */
    private $path;

    /**
     * @var bool
     */
    private $isDeleted;

    /**
     * When calling from the constructor, the file must always exist, if the file is to be created
     * the self::create method must be called.
     *
     * @throws FileExistsException
     * @throws FileTypeException
     */
    public function __construct(
        string $file,
        FileTreeInterface $tree = null
    ) {
        $this->path = is_link($file) ? $file : realpath($file);

        if (!$this->path) {
            throw new FileExistsException(sprintf('File %s does not exists, if you want to create it please use: %s::%s', $file, __CLASS__, 'create'));
        }

        if (is_dir($this->path)) {
            throw new FileTypeException(sprintf('Invalid file type provided, expected file, got directory: "%s"', $file));
        }

        $this->_tObserveTreeTrait = $tree;
    }

    public static function create(
        string $file,
        string $contents = '',
        int $permissions = 0644,
        bool $overwrite = false
    ): FileInterface {
        if (false === $overwrite && file_exists($file)) {
            throw new FileExistsException("File \"$file\" already exists!");
        }

        if (false === file_put_contents($file, $contents)) {
            throw new FileWriteException("Failed to create file: $file");
        }

        return (new self($file))
            ->chmod($permissions);
    }

    //<editor-fold desc="FileInterface methods">
    public function delete(): void
    {
        $this->checkIfDeleted(__METHOD__);
        $this->_tObserveTreeTraitRefreshTree();
        FileHelper::delete($this->toString());
        $this->isDeleted = true;
    }

    public function getType(): string
    {
        return FileHelper::getType((string) $this);
    }

    public function move(string $dest, bool $overwrite = false): LDLFileInterface
    {
        return FileHelper::move($this->path, $dest, $overwrite);
    }

    public function chmod(int $permissions): LDLFileInterface
    {
        $this->checkIfDeleted(__METHOD__);
        FilePermsHelper::chmod($this->toString(), $permissions);

        return $this;
    }

    public function getPerms(): int
    {
        return FilePermsHelper::getPerms($this->toString());
    }

    public function getLines(): StringCollection
    {
        $this->checkIfDeleted(__METHOD__);

        return FileHelper::getLines($this->toString());
    }

    public function getLinesAsString(string $separator = ''): string
    {
        $this->checkIfDeleted(__METHOD__);

        return FileHelper::getLinesAsString($this->toString(), $separator);
    }

    public function iterateLines(): iterable
    {
        $this->checkIfDeleted(__METHOD__);
        yield from FileHelper::iterateLines($this->toString());
    }

    public function put(string $contents, string $newName = null): FileInterface
    {
        $file = trim($newName ?? $this->toString());

        if (!is_writable($file)) {
            throw new FileWriteException("File \"$file\" is not writable");
        }

        if ('' === $file) {
            throw new \InvalidArgumentException('File name can not be empty');
        }

        if (false === file_put_contents($file, $contents)) {
            throw new FileWriteException("Failed to write to file: {$this->path}");
        }

        return new self($file);
    }

    public function append(string $content): FileInterface
    {
        $this->checkIfDeleted(__METHOD__);

        if (!is_writable($this->toString())) {
            throw new FileWriteException("File \"$this\" is not writable");
        }

        file_put_contents($this->toString(), $content, \FILE_APPEND);

        return $this;
    }

    public function link(string $dest, bool $force = false): LinkInterface
    {
        return Link::create($this->path, $dest, $force);
    }

    public function copy(string $dest, bool $overwrite): LDLFileInterface
    {
        FileHelper::copy($this->toString(), $dest, $overwrite);

        return new self($dest);
    }

    public function getDirectory(int $levels = null): DirectoryInterface
    {
        $levels = $levels ?? 1;
        $this->checkIfDeleted(__METHOD__);

        return new Directory(dirname($this->path, $levels));
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function isReadable(): bool
    {
        return is_readable($this->path);
    }

    public function isWritable(): bool
    {
        return is_writable($this->path);
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getExtension(): string
    {
        if (!preg_match('#\.[a-zA-Z]#', $this->path)) {
            return '';
        }

        return mb_substr($this->path, mb_strrpos($this->path, '.') + 1);
    }

    //</editor-fold>

    //<editor-fold desc="ToStringInterface methods">
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

        $msg = sprintf('Can not %s from file "%s", it has been deleted', $operation, $this->toString());

        throw new FileExistsException($msg);
    }
    //</editor-fold>
}
