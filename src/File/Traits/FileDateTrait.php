<?php

declare(strict_types=1);

namespace LDL\File\Traits;

trait FileDateTrait
{
    public function getDateCreated(): \DateTimeInterface
    {
        return \DateTime::createFromFormat('U', (string) filemtime($this->getPath()));
    }

    public function getDateAccessed(): \DateTimeInterface
    {
        return \DateTime::createFromFormat('U', (string) fileatime($this->getPath()));
    }
}
