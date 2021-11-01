<?php declare(strict_types=1);

namespace LDL\File\Validator;

use LDL\File\Helper\PathHelper;
use LDL\Validators\NegatedValidatorInterface;
use LDL\Validators\Traits\NegatedValidatorTrait;
use LDL\Validators\Traits\ValidatorDescriptionTrait;
use LDL\Validators\Traits\ValidatorValidateTrait;
use LDL\Validators\ValidatorHasConfigInterface;
use LDL\Validators\ValidatorInterface;

class PathValidator implements ValidatorInterface, NegatedValidatorInterface, ValidatorHasConfigInterface
{
    use ValidatorValidateTrait;
    use NegatedValidatorTrait;
    use ValidatorDescriptionTrait;

    /**
     * @var string
     */
    private $path;

    public function __construct(
        string $path,
        bool $negated=false,
        string $description=null
    )
    {
        $this->path = PathHelper::getAbsolutePath($path);
        $this->_tNegated = $negated;

        if(null !== $description){
            $this->_tDescription = $description;
            return;
        }

        $this->_tDescription = sprintf(
            'Path must%smatch with: %s',
            $negated ? ' NOT ' : ' ',
            $this->path
        );
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    public function assertTrue($value): void
    {
        if(mb_strpos($value, $this->path) !== false){
            return;
        }

        throw new \LogicException("Path: \"$value\" doesn't matches criteria \"{$this->path}\"");
    }

    public function assertFalse($value): void
    {
        if(mb_strpos($value, $this->path) === false){
            return;
        }

        throw new \LogicException("Path: \"$value\" matches criteria \"{$this->path}\"");
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
        if(!array_key_exists('path', $data)){
            $msg = sprintf("Missing property 'path' in %s", __CLASS__);
            throw new Exception\FileValidatorException($msg);
        }

        return new self(
            $data['path'],
            array_key_exists('negated', $data) ? (bool)$data['negated'] : false,
            $data['description'] ?? null
        );
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return [
            'path' => $this->path,
            'negated' => $this->_tNegated,
            'description' => $this->getDescription()
        ];
    }
}