<?php declare(strict_types=1);

namespace LDL\File\Exception;

use LDL\Framework\Base\Exception\LDLException;

/**
 * Class FileException
 * @package LDL\File\Exception
 *
 * This is an abstract exception which all file exceptions extend to, this is done in order to provide
 * friendliness to the end user, i.e: Instead of catching each type of exception for file classes / helpers
 * one can just simply catch FileException and call it a day.
 */

abstract class FileException extends LDLException
{

}