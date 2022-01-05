<?php

declare(strict_types=1);

namespace LDL\File\Contracts;

use LDL\Type\Collection\Types\String\StringCollection;

interface ReadFileLinesInterface
{
    /**
     * @throws \Exception
     */
    public function getLines(): StringCollection;

    /**
     * Returns lines from the file as a PHP string.
     */
    public function getLinesAsString(string $separator): string;

    /**
     * @throws \Exception
     */
    public function iterateLines(): iterable;
}
