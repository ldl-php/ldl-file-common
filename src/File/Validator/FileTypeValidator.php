<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\File\Constants\FileTypeConstants;
use LDL\Type\Collection\Types\String\UniqueStringCollection;
use LDL\Validators\NegatedValidatorInterface;
use LDL\Validators\Traits\NegatedValidatorTrait;
use LDL\Validators\Traits\ValidatorValidateTrait;
use LDL\Validators\ValidatorHasConfigInterface;
use LDL\Validators\ValidatorInterface;
use LDL\File\Helper\FileHelper;

class FileTypeValidator implements ValidatorInterface, NegatedValidatorInterface, ValidatorHasConfigInterface
{
    use ValidatorValidateTrait;
    use NegatedValidatorTrait;

    /**
     * @var UniqueStringCollection
     */
    private $types;

    /**
     * @var string|null
     */
    private $description;

    public function __construct(iterable $types, bool $negated=false, string $description=null)
    {
        $validTypes = new UniqueStringCollection([
            FileTypeConstants::FILE_TYPE_DIRECTORY,
            FileTypeConstants::FILE_TYPE_REGULAR,
            FileTypeConstants::FILE_TYPE_LINK,
            FileTypeConstants::FILE_TYPE_SOCKET,
            FileTypeConstants::FILE_TYPE_FIFO,
            FileTypeConstants::FILE_TYPE_CHAR,
            FileTypeConstants::FILE_TYPE_BLOCK,
            FileTypeConstants::FILE_TYPE_UNKNOWN
        ]);

        $types = new UniqueStringCollection($types);

        if(count($types) === 0){
            throw new \InvalidArgumentException(
                'At least one the following file types must be specified: "%s"',
                $validTypes->implode(', ')
            );
        }

        $types->map(static function($item) use ($validTypes){
            if($validTypes->hasValue($item)){
                return $item;
            }

            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid file type specified "%s", valid file types are: "%s"',
                    $item,
                    $validTypes->implode(', ')
                )
            );
        });

        $this->types = $types;
        $this->_tNegated = $negated;
        $this->description = $description;
    }

    /**
     * @return UniqueStringCollection
     */
    public function getTypes(): UniqueStringCollection
    {
        return $this->types;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        if(!$this->description){
            return sprintf(
                'File type must be one of: %s',
                $this->types->implode(',')
            );
        }

        return $this->description;
    }

    public function assertTrue($path): void
    {
        if($this->types->hasValue(FileHelper::getType($path))){
            return;
        }

        throw new \InvalidArgumentException('File type criteria not satisfied');
    }

    public function assertFalse($path): void
    {
        if(!$this->types->hasValue(FileHelper::getType($path))){
            return;
        }

        throw new \InvalidArgumentException('File type criteria not satisfied');
    }

    public function jsonSerialize(): array
    {
        return $this->getConfig();
    }

    /**
     * @param array $data
     * @return ValidatorInterface
     * @throws Exception\FileValidatorException
     */
    public static function fromConfig(array $data = []): ValidatorInterface
    {
        if(false === array_key_exists('types', $data)){
            $msg = sprintf("Missing property 'types' in %s", __CLASS__);
            throw new Exception\FileValidatorException($msg);
        }

        if(!is_iterable($data['types'])){
            $msg = sprintf("'types' property is not iterable in %s", __CLASS__);
            throw new Exception\FileValidatorException($msg);
        }

        try{
            return new self(
                $data['types'],
                array_key_exists('negated', $data) ? (bool)$data['negated'] : false,
                $data['description'] ?? null
            );
        }catch(\Exception $e){
            throw new Exception\FileValidatorException($e->getMessage());
        }
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'types' => $this->types->toArray(),
            'negated' => $this->_tNegated,
            'description' => $this->getDescription()
        ];
    }

}