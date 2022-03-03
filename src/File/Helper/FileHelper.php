<?php

declare(strict_types=1);

namespace LDL\File\Helper;

use LDL\File\Constants\FileTypeConstants;
use LDL\File\Contracts\FileInterface;
use LDL\File\Exception\FileExistsException;
use LDL\File\Exception\FileReadException;
use LDL\File\Exception\FileTypeException;
use LDL\File\Exception\FileWriteException;
use LDL\File\File;
use LDL\Type\Collection\Types\String\StringCollection;

final class FileHelper
{
    /**
     * @throws FileExistsException if file does not exists
     * @throws FileReadException   if file permissions could not be read
     */
    public static function getType(string $path): string
    {
        if (!file_exists($path)) {
            throw new FileExistsException('Can not get permissions from a file which does not exists');
        }

        $perms = fileperms($path);

        if (false === $perms) {
            throw new FileReadException('Could not get file permissions');
        }

        switch ($perms & 0xF000) {
            case 0xC000: // socket
                return FileTypeConstants::FILE_TYPE_SOCKET;
                break;
            case 0xA000: // symbolic link
                return FileTypeConstants::FILE_TYPE_LINK;
                break;
            case 0x8000: // regular
                return FileTypeConstants::FILE_TYPE_REGULAR;
                break;
            case 0x6000: // block special
                return FileTypeConstants::FILE_TYPE_BLOCK;
                break;
            case 0x4000: // directory
                return FileTypeConstants::FILE_TYPE_DIRECTORY;
                break;
            case 0x2000: // character special
                return FileTypeConstants::FILE_TYPE_CHAR;
                break;
            case 0x1000: // FIFO pipe
                return FileTypeConstants::FILE_TYPE_FIFO;
                break;
            default: // unknown
                return FileTypeConstants::FILE_TYPE_UNKNOWN;
                break;
        }
    }

    /**
     * Read lines from a file.
     *
     * @throws FileTypeException
     * @throws FileReadException
     */
    public static function getLines(string $file): StringCollection
    {
        if (!is_readable($file)) {
            throw new FileReadException("File $file is not readable!");
        }

        if (is_dir($file)) {
            throw new FileTypeException("$file is a directory");
        }

        return new StringCollection(file($file));
    }

    /**
     * Returns lines from a file as a PHP string.
     *
     * @throws FileReadException
     * @throws FileTypeException
     */
    public static function getLinesAsString(string $file, ?string $separator): string
    {
        if (!is_readable($file)) {
            throw new FileReadException("File $file is not readable!");
        }

        if (is_dir($file)) {
            throw new FileTypeException("$file is a directory");
        }

        $lines = file($file);

        if (null === $separator) {
            $lineEnding = preg_replace('/.*/', '', $lines[0]);
            $separator = $lineEnding[0];
        }

        return implode($separator, file($file));
    }

    /**
     * Iterate through the lines of a file in a memory efficient way by using generators
     * Useful to traverse large files line by line.
     *
     * @throws FileTypeException
     * @throws FileReadException
     */
    public static function iterateLines(string $file): iterable
    {
        if (!is_readable($file)) {
            throw new FileReadException("File $file is not readable!");
        }

        if (is_dir($file)) {
            throw new FileTypeException("$file is a directory");
        }

        $fp = fopen($file, 'rb');

        while ($line = fgets($fp)) {
            yield $line;
        }

        fclose($fp);
    }

    /**
     * Copy a file from $source to $dest, if the $dest file exists, and the overwrite flag is set to true
     * the file will be overwritten.
     *
     * @throws FileExistsException
     * @throws FileReadException
     * @throws FileTypeException
     * @throws FileWriteException
     */
    public static function copy(string $source, string $dest, bool $overwrite = false): FileInterface
    {
        if (is_dir($dest)) {
            $dest = FilePathHelper::createAbsolutePath($dest, basename($source));
        }

        if (!is_readable($source)) {
            throw new FileReadException("Source file \"$source\" is not readable");
        }

        if (false === $overwrite && file_exists($dest)) {
            $msg = sprintf(
                'Destination file %s already exists, if you really want to overwrite it, set the overwrite flag to true',
                $dest
            );
            throw new FileWriteException($msg);
        }

        if (!copy($source, $dest)) {
            throw new FileWriteException('Could not copy file from "%s" to "%s"', $source, $dest);
        }

        return new File($dest);
    }

    /**
     * @throws FileExistsException
     * @throws FileReadException
     * @throws FileWriteException
     * @throws FileTypeException
     */
    public static function delete(string $file): void
    {
        $isLink = is_link($file);

        if (!$isLink && !file_exists($file)) {
            throw new FileExistsException("File \"$file\" does not exists!");
        }

        if (is_dir($file)) {
            throw new FileTypeException("File $file is not a file");
        }

        if (!$isLink && !is_readable($file)) {
            throw new FileReadException("File \"$file\" is not readable");
        }

        if (false === unlink($file)) {
            throw new FileWriteException("Could not delete file: \"$file\"");
        }
    }

    /**
     * @throws FileExistsException
     * @throws FileReadException
     * @throws FileTypeException
     * @throws FileWriteException
     */
    public static function move(string $source, string $dest, bool $overwrite = false): FileInterface
    {
        $return = self::copy($source, $dest, $overwrite);
        self::delete($source);

        return $return;
    }

    /**
     * returns file mime type.
     *
     * @throws FileExistsException
     * @throws FileReadException
     */
    public static function getMimeType(string $file, ?string $mimeDatabase = null): ?string
    {
        if (null !== $mimeDatabase) {
            $mimeDatabase = new File($mimeDatabase);
        }

        $file = new File($file);

        if (!$file->isReadable()) {
            throw new FileReadException(sprintf('File %s is not readable', $file->getPath()));
        }

        $finfo = null === $mimeDatabase ?
            finfo_open(\FILEINFO_MIME) :
            finfo_open(\FILEINFO_MIME, $mimeDatabase->getPath());

        $mimeType = finfo_file($finfo, $file->getPath());

        finfo_close($finfo);

        return $mimeType ?: null;
    }

    /**
     * Creates a temporary file in the system's temp directory.
     *
     * @throws FileExistsException
     * @throws FileTypeException
     * @throws FileWriteException
     */
    public static function createSysTempFile(string $name = null, string $contents = '', int $perms = 0600): FileInterface
    {
        return DirectoryHelper::getSysTempDir()
            ->mkfile(
                $name ?? sha1(uniqid('ldl_', true)),
                $contents,
                $perms,
                true
            );
    }
}
