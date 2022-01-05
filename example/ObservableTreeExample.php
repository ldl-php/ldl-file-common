<?php

declare(strict_types=1);

/**
 * This example observes the created test directories tree, when a random nested directory is deleted
 * the directory is also removed from within the tree.
 */

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/lib/example-helper.php';

use LDL\File\Constants\FileTypeConstants;

echo "Create test directory ...\n\n";

$tempDir = createTestFiles();

$tree = $tempDir->getTree();

echo "Traverse directory tree and pick a random nested directory ...\n\n";

/**
 * @var \LDL\File\Contracts\DirectoryInterface $randomDirectory
 */
$randomDirectory = null;

foreach ($tree->traverse() as $file) {
    if (
        null === $randomDirectory &&
        $file->getDirectory()->getPath() !== $tempDir->getPath() &&
        FileTypeConstants::FILE_TYPE_DIRECTORY === $file->getType()
    ) {
        $randomDirectory = $file;
    }
    echo $file->getPath()."\n";
}

echo "\nGot random nested directory $randomDirectory\n\n";

echo "Delete random nested directory\n\n";

$randomDirectory->delete();

//dump($randomDirectory->isDeleted());

echo "Verify that the directory is not within the tree any more\n\n";

foreach ($tree->traverse() as $file) {
    if ($file->getPath() === $randomDirectory->getPath()) {
        dd($file->getPath(), $randomDirectory->getPath());
    }
}
