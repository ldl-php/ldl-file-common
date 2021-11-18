<?php declare(strict_types=1);

namespace LDL\File\Collection\Contracts;

use LDL\File\FileTree;

interface FilterByFileTypeInterface
{

    public function filterByFileType(string $type) : FileTree;

}
