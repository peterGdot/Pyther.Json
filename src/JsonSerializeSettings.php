<?php
namespace Pyther\Json;

use Pyther\Json\Types\EnumFormat;

/**
 * Json settings for serialization.
 */
class JsonSerializeSettings extends JsonSettings
{
    // flags
    private const USE_PRETTY_PRINT = 1;
    private const DATETIME_AS_STRING = 2;
    private int $flags = self::USE_PRETTY_PRINT | self::DATETIME_AS_STRING;

    private EnumFormat $enumFormat = EnumFormat::Value;
    
    /**
     * Enable or disable json indention (enabled by default).
     * @param boolean $value
     * @return static
     */
    public function setPrettyPrint(bool $value = true): static {
        $this->flags = $value ? $this->flags | self::USE_PRETTY_PRINT : $this->flags & ~self::USE_PRETTY_PRINT;
        return $this;
    }
    
    public function getPrettyPrint(): bool {
        return ($this->flags & self::USE_PRETTY_PRINT) != 0;
    }
    
    /**
     * Enable or disable to serialize DateTime as string (enabled by default).
     * @param boolean $value
     * @return static
     */
    public function setDateTimeAsString(bool $value = true): static {
        $this->flags = $value ? $this->flags | self::DATETIME_AS_STRING : $this->flags & ~self::DATETIME_AS_STRING;
        return $this;
    }
    
    public function getDateTimeAsString(): bool {
        return ($this->flags & self::DATETIME_AS_STRING) != 0;
    }
    
    /**
     * Defines the default serialization format for enumerations (EnumFormat::Value by default). 
     * @param EnumFormat $format
     * @return static
     */
    public function setEnumFormat(EnumFormat $format): static {
        $this->enumFormat = $format;
        return $this;
    }

    public function getEnumFormat(): EnumFormat {
        return $this->enumFormat;
    }
}