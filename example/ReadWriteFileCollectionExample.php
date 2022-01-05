<?php

declare(strict_types=1);

use LDL\File\Collection\ReadWriteFileCollection;

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/lib/example-helper.php';

use LDL\File\Constants\FileTypeConstants;
use LDL\Validators\Exception\ValidatorException;

$tmpDir = createTestFiles();

$tree = $tmpDir->getTree();
randomizePermissions($tmpDir);

$collection = new ReadWriteFileCollection();

/*
 * Assign readable and non readable permissions to files which are NOT links
 */
foreach ($tree->filterByFileType(FileTypeConstants::FILE_TYPE_LINK, true) as $i => $file) {
    try {
        $collection->append($file);
    } catch (ValidatorException $e) {
        echo "OK: {$e->getMessage()}\n";
    }
}

$tmpDir->chmod(0755, true);
$tmpDir->delete();

echo "Done\n";
