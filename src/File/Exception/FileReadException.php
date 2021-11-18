<?php declare(strict_types=1);

namespace LDL\File\Exception;

/**
 * Class ReadException
 * @package LDL\File\Exception
 *
 * This exception must be thrown when a file or directory are required to be readable, do not mistake this exception
 * with the ExistsException as this one refers to file permissions, not to file existence.
 */

class FileReadException extends FileException
{

}