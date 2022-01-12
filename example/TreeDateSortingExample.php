<?php

declare(strict_types=1);

/**
 * This example observes the created test directories tree, when a random nested directory is deleted
 * the directory is also removed from within the tree.
 */

require __DIR__.'/../vendor/autoload.php';

use LDL\File\Contracts\LDLFileInterface;
use LDL\File\Directory;
use LDL\File\FileTree;
use LDL\Framework\Base\Constants;

$tree = new FileTree(new Directory(__DIR__));

echo "\nSort tree by date created ...\n";
echo "#########################################################\n\n";
/**
 * @var LDLFileInterface $file
 */
foreach ($tree->sortByDateCreated(Constants::SORT_DESCENDING) as $file) {
    echo sprintf('%s - %s%s', $file->getDateCreated()->format('Y-m-d H:i:s'), $file, "\n");
}

echo "\nSort tree by date accessed ...\n";
echo "#########################################################\n\n";

/**
 * @var LDLFileInterface $file
 */
foreach ($tree->sortByDateAccessed(Constants::SORT_DESCENDING) as $file) {
    echo sprintf('%s - %s%s', $file->getDateAccessed()->format('Y-m-d H:i:s'), $file, "\n");
}
