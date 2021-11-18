<?php declare(strict_types=1);

namespace LDL\File\Collection\Traits;

use LDL\File\Contracts\FileInterface;
use LDL\File\Contracts\ReadFileLinesInterface;
use LDL\File\File;
use LDL\Framework\Helper\ClassHelper;
use LDL\Type\Collection\Types\String\StringCollection;

trait ReadFileLinesInterfaceTrait
{

    //<editor-fold desc="ReadFileLinesInterface methods">
    /**
     * Reads all lines from a file collection and returns them as a string collection
     * @return StringCollection
     * @throws \Exception
     */
    public function getLines() : StringCollection
    {
        ClassHelper::mustHaveInterface(get_class($this), ReadFileLinesInterface::class);

        $return = new StringCollection();

        /**
         * @var FileInterface $file
         */
        foreach($this as $file){
            $return->appendMany($file->getLines());
        }

        return $return;
    }

    /**
     * Iterate through each line
     * @return iterable
     */
    public function iterateLines() : iterable
    {
        /**
         * @var FileInterface $file
         */
        foreach($this as $file){
            $fp = fopen($file->getPath(), 'rb');

            while($line = fgets($fp)){
                yield $line;
            }
            fclose($fp);
        }
    }
    //</editor-fold>

}
