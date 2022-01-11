<?php

declare(strict_types=1);

namespace LDL\File;

use LDL\File\Constants\FileTypeConstants;
use LDL\File\Contracts\LDLFileInterface;
use LDL\File\Contracts\LinkInterface;
use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileTypeException;
use LDL\File\Helper\FileHelper;
use LDL\File\Helper\FilePathHelper;
use LDL\File\Helper\LinkHelper;
use LDL\File\Traits\FileDateTrait;
use LDL\File\Traits\FileDeleteCheckTrait;
use LDL\File\Traits\FileGetDirectoryTrait;

final class Link implements LinkInterface
{
    use FileDateTrait;
    use FileDeleteCheckTrait;
    use FileGetDirectoryTrait;

    /**
     * @var bool
     */
    private $isDeleted;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $target;

    /**
     * When calling from the constructor, the file must always exist, if the file is to be created
     * the self::create method must be called.
     *
     * @throws FileExistsException
     * @throws FileTypeException
     */
    public function __construct(string $file)
    {
        if (!file_exists($file)) {
            throw new FileExistsException("File $file does not exists");
        }

        if (!is_link($file)) {
            throw new FileTypeException("File $file is not a link");
        }

        $target = readlink($file);
        $this->target = is_dir($target) ? new Directory($target) : new File($target);
        $this->path = FilePathHelper::createAbsolutePath($file);
        $this->isDeleted = false;
    }

    public function getType(): string
    {
        return FileTypeConstants::FILE_TYPE_LINK;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public static function create(
        string $source,
        string $destination,
        bool $force = false
    ): LinkInterface {
        return LinkHelper::create($source, $destination, $force);
    }

    public function delete(): void
    {
        $this->checkIfDeleted(__METHOD__);
        FileHelper::delete($this->path);
        $this->isDeleted = true;
    }

    /**
     * @throws Exception\FileReadException
     * @throws Exception\FileWriteException
     * @throws FileExistsException
     * @throws FileTypeException
     */
    public function copy(string $dest, bool $overwrite = false): LDLFileInterface
    {
        return self::create($this->target->getPath(), $dest);
    }

    public function move(string $dest, bool $overwrite = false): LDLFileInterface
    {
        $this->checkIfDeleted(__METHOD__);
        $link = self::create($this->target->getPath(), $dest, $overwrite);
        $this->delete();
        $this->isDeleted = true;

        return $link;
    }

    public function getPerms(): int
    {
        return fileperms($this->path);
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

    public function isDeleted(): bool
    {
        return $this->isDeleted;
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
}
