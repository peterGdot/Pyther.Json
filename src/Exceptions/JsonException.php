<?php
namespace Pyther\Json\Exceptions;

class JsonException extends \Exception
{        
    private ?string $propertyName;

    public function __construct(string $message, string $propertyName = null, $code = 0, \Throwable $previous = null) {
        if (!empty($propertyName)) {
            $message = "JSON Error on property '$propertyName': $message";
        } else {
            $message = "JSON Error: $message";
        }
        $this->propertyName = $propertyName;
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function getPropertyName(): ?string {
        return $this->propertyName;
    }
}