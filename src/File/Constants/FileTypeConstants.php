<?php declare(strict_types=1);

namespace LDL\File\Helper\Constants;

final class FileTypeConstants
{
    public const FILE_TYPE_REGULAR='regular';
    public const FILE_TYPE_DIRECTORY='directory';
    public const FILE_TYPE_LINK='link';
    public const FILE_TYPE_SOCKET='socket';
    public const FILE_TYPE_FIFO='fifo';
    public const FILE_TYPE_CHAR='char';
    public const FILE_TYPE_BLOCK='block';
    public const FILE_TYPE_UNKNOWN='unknown';
}
