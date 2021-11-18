<?php declare(strict_types=1);

namespace LDL\File\Collection;

use LDL\File\Collection\Contracts\FileCollectionInterface;
use LDL\File\Collection\Traits\AppendFileAsStringTrait;
use LDL\File\Constants\FileTypeConstants;
use LDL\File\Contracts\FileInterface;
use LDL\File\File;
use LDL\Framework\Helper\IterableHelper;
use LDL\Type\Collection\AbstractTypedCollection;
use LDL\Type\Collection\Traits\Validator\AppendValueValidatorChainTrait;
use LDL\Validators\InterfaceComplianceValidator;

final class FileCollection extends AbstractTypedCollection implements FileCollectionInterface
{
    use AppendValueValidatorChainTrait;
    use AppendFileAsStringTrait;

    public function __construct(iterable $items = null)
    {
        $this->getAppendValueValidatorChain()
            ->getChainItems()
            ->appendMany([
                new InterfaceComplianceValidator(FileInterface::class),
            ])
            ->lock();

        parent::__construct($items);
    }

    /**
     * Filters hidden files (files which start with a .)
     *
     * @return FileCollection
     */
    public function filterHiddenFiles() : FileCollection
    {
        /**
         * @var FileCollection $return
         */
        $return =$this->filter(
        static function(File $f){
            return strpos($f->getFilename(), '.') === 0;
        });

        return $return;
    }

    /**
     * @param string $extension
     * @return FileCollection
     * @throws \LDL\File\Exception\FileExistsException
     * @throws \LDL\File\Exception\FileTypeException
     */
    public function filterByExtension(string $extension) : FileCollection
    {
        return $this->filterByExtensions([$extension]);
    }

    /**
     * Filters files which contain a certain extension
     *
     * @param iterable $extensions
     * @return FileCollection
     * @throws \LDL\File\Exception\FileExistsException
     * @throws \LDL\File\Exception\FileTypeException
     */
    public function filterByExtensions(iterable $extensions) : FileCollection
    {
        $extensions = IterableHelper::toArray($extensions);

        $return = new FileCollection();

        /**
         * @var File $file
         */
        foreach($this as $file){
            if(in_array($file->getExtension(), $extensions, true)){
                $return->append($file);
            }
        }

        return $return;
    }

    /**
     * Filters files which correspond to a file type
     *
     * @see FileTypeConstants for available types
     *
     * @param string $type
     * @return FileCollection
     * @throws \LDL\File\Exception\FileExistsException
     * @throws \LDL\File\Exception\FileTypeException
     * @throws \LDL\File\Exception\FileReadException
     */
    public function filterByFileType(string $type) : FileCollection
    {
        return $this->filterByFileTypes([$type]);
    }

    /**
     * Filters current file collection by file types
     *
     * @see FileTypeConstants for available types
     *
     * @param iterable $types
     * @return FileCollection
     * @throws \LDL\File\Exception\FileExistsException
     * @throws \LDL\File\Exception\FileTypeException
     * @throws \LDL\File\Exception\FileReadException
     */
    public function filterByFileTypes(iterable $types) : FileCollection
    {
        $types = IterableHelper::toArray($types);

        $filtered = new FileCollection();

        /**
         * @var File $file
         */
        foreach($this as $file){
            if(in_array($file->getType(), $types, true)){
                $filtered->append($file);
            }
        }

        return $filtered;
    }

}