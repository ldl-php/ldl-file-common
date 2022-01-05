<?php

declare(strict_types=1);

namespace LDL\File\Traits;

use LDL\File\Exception\FileExistsException;

trait DeleteCheckTrait
{
    private function checkIfDeleted(string $operation): void
    {
        if (!$this->isDeleted()) {
            return;
        }

        $msg = sprintf('Can not %s link "%s", it has been deleted', $operation, $this->getPath());

        throw new FileExistsException($msg);
    }
}
