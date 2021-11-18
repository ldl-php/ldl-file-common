<?php declare(strict_types=1);

namespace LDL\File\Contracts;

use LDL\Type\Collection\Types\String\StringCollection;

interface ReadFileLinesInterface
{
    /**
     * @throws \Exception
     * @return StringCollection
     */
    public function getLines() : StringCollection;

    /**
     * @throws \Exception
     * @return iterable
     */
    public function iterateLines() : iterable;
}