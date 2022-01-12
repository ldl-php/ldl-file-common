<?php

declare(strict_types=1);

namespace LDL\File\Traits;

use LDL\File\Helper\FileOwnershipHelper;

trait FileOwnershipTrait
{
    public function chown(?string $user, ?string $group): void
    {
        FileOwnershipHelper::chown($this->getPath(), $user, $group);
    }

    public function getOwner(): int
    {
        return FileOwnershipHelper::getOwner($this->getPath());
    }

    public function getGroup(): int
    {
        return FileOwnershipHelper::getGroup($this->getPath());
    }
}
