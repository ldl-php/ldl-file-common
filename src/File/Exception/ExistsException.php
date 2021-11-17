<?php declare(strict_types=1);

namespace LDL\File\Exception;

/**
 * Class ExistsException
 * @package LDL\File\Exception
 *
 * This exception must be thrown when a file or directory do not exist and existence is required.
 * Do not mistake ExistsException with ReadException as they are two different things.
 */

class ExistsException extends FileException
{

}