<?php declare(strict_types=1);

use LDL\File\Collection\JsonFileCollection;
use LDL\File\Validator\Exception\JsonFileDecodeException;
use LDL\Validators\Chain\Dumper\ValidatorChainExprDumper;
use LDL\Validators\Chain\Dumper\ValidatorChainHumanDumper;

require __DIR__.'/../vendor/autoload.php';
require __DIR__.'/lib/example-helper.php';

$tmpDir = createTestFiles();

echo sprintf(
    'Create "%s" instance%s',
    JsonFileCollection::class,
    "\n\n"
);

$jsonCollection = new JsonFileCollection();

echo "Check validators\n";

dump(ValidatorChainExprDumper::dump($jsonCollection->getAppendValueValidatorChain()));
dump(ValidatorChainHumanDumper::dump($jsonCollection->getAppendValueValidatorChain()));

$json = [
    'name' => 'name',
    'lastname' => 'lastname'
];

$jsonFile = $tmpDir->mkfile('test.json', json_encode($json,\JSON_THROW_ON_ERROR));

echo "Append JSON file to the collection (no exception must be thrown) ...\n";

$jsonCollection->append($jsonFile);

echo "OK!\n";

try {

    echo "Append regular file to the collection, EXCEPTION must be thrown\n";
    $jsonCollection->append(__FILE__);

}catch(JsonFileDecodeException $e) {

    echo "EXCEPTION: {$e->getMessage()}\n";

}

echo "\nClean up generated files ...\n";

deleteTestDir();
