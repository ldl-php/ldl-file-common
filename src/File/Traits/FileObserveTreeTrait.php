<?php

declare(strict_types=1);

namespace LDL\File\Traits;

use LDL\File\Contracts\FileTreeInterface;

trait FileObserveTreeTrait
{
    /**
     * @var FileTreeInterface|null
     */
    private $_tObserveTreeTrait;

    private function _tObserveTreeTraitRefreshTree(): void
    {
        if (null === $this->_tObserveTreeTrait) {
            return;
        }

        $this->_tObserveTreeTrait->removeByKey($this->getPath());
    }
}
