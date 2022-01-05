<?php

declare(strict_types=1);

namespace LDL\File\Traits;

use LDL\File\Contracts\DirectoryInterface;
use LDL\File\Directory;

trait GetDirectoryTrait
{
    public function getDirectory(int $levels = null): DirectoryInterface
    {
        $levels = $levels ?? 1;
        $this->checkIfDeleted(__METHOD__);

        return new Directory(dirname($this->getPath(), $levels));
    }
}
