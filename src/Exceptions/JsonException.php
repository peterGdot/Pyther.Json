<?php
namespace Pyther\Json\Exceptions;

/**
 * Excepion for any kind of (de)serialization erros.
 */
class JsonException extends \Exception
{        
    private ?string $propertyName;

    /**
     * Create a new json exception instance.
     *
     * @param string $message The exception message.
     * @param string|null $propertyName The optional property this exception belongs to.
     * @param integer $code The optional exception code.
     * @param \Throwable|null $previous
     */
    public function __construct(string $message, string $propertyName = null, $code = 0, \Throwable $previous = null) {
        if (!empty($propertyName)) {
            $message = "JSON Error on property '$propertyName': $message";
        } else {
            $message = "JSON Error: $message";
        }
        parent::__construct($message, $code, $previous);
        $this->propertyName = $propertyName;
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    /**
     * Get the optional property name this exceptions belongs to.
     *
     * @return string|null
     */
    public function getPropertyName(): ?string {
        return $this->propertyName;
    }
}