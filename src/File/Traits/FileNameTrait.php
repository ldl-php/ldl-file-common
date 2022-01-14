<?php

declare(strict_types=1);

namespace LDL\File\Traits;

trait FileNameTrait
{
    public function getName(): string
    {
        return basename($this->getPath());
    }
}
