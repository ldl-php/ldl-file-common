<?php declare(strict_types=1);

namespace LDL\File\Exception;

/**
 * Class FileTypeException
 * @package LDL\File\Exception
 *
 * This exception must be thrown when the type of the file is not what you expect, example when you expect
 * a directory but a regular file was given or vice versa.
 */
class FileTypeException extends FileException
{

}