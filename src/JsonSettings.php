<?php
namespace Pyther\Json;

use Pyther\Json\NamingPolicies\BaseNamingPolicy;
use Pyther\Json\Types\EnumFormat;

/**
 * Json settings for serialization and deserialization.
 */
class JsonSettings
{
    // flags
    private const INCLUDE_PROTECTED_FLAG = 1;
    private const SKIP_INHERITED_PROPERTIES = 2;    
    // on serialization only
    private const USE_PRETTY_PRINT = 4;
    private const DATETIME_AS_STRING = 8;
    private const SKIP_NULL = 16;
    private const SKIP_EMPTY_ARRAY = 32;

    private int $flags = self::USE_PRETTY_PRINT | self::DATETIME_AS_STRING;
    private ?BaseNamingPolicy $namingPolicy = null;
    private string $dataTimeFormat = \DateTime::W3C;
    // on serialization only
    private EnumFormat $enumFormat = EnumFormat::Value;

    /**
     * Set to true, to enable handling protected properties.
     *
     * @param boolean $value
     * @return static Returns the object itself for chaining.
     */
    public function setIncludeProtected(bool $value = true): static {
        $this->flags = $value ? $this->flags | self::INCLUDE_PROTECTED_FLAG : $this->flags & ~self::INCLUDE_PROTECTED_FLAG;
        return $this;
    }

    public function getIncludeProtected(): bool {
        return ($this->flags & self::INCLUDE_PROTECTED_FLAG) != 0;
    }

    /**
     * The naming policy (1:1 by default).
     * 
     * @var BaseNamingPolicy|null
     */
    public function setNamingPolicy(?BaseNamingPolicy $policy): static {
        $this->namingPolicy = $policy;
        return $this;
    }

    public function getNamingPolicy(): ?BaseNamingPolicy {
        return $this->namingPolicy;
    }


    /**
     * Set the default date time format (\DateTime::W3C by default).
     * This can be overwriten per Property using "#[JsonDateTime(...)]"
     *
     * @param string $value
     * @return static
     */
    public function setDateTimeFormat(string $value): static {
        $this->dataTimeFormat = $value;
        return $this;
    }

    public function getDateTimeFormat(): string {
        return $this->dataTimeFormat;
    }

    /**
     * Define to skip ingerited properties (false by default).
     * @param boolean $value
     * @return static
     */
    public function setSkipInheritedProperties(bool $value = true): static {
        $this->flags = $value ? $this->flags | self::SKIP_INHERITED_PROPERTIES : $this->flags & ~self::SKIP_INHERITED_PROPERTIES;
        return $this;
    }

    public function getSkipInheritedProperties(): bool {
        return ($this->flags & self::SKIP_INHERITED_PROPERTIES) != 0;
    }    

    #region on serialization only   
    
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

    /**
     * Define to skip null values.
     * @param boolean $value
     * @return static
     */
    public function setSkipNull(bool $value = true): static {
        $this->flags = $value ? $this->flags | self::SKIP_NULL : $this->flags & ~self::SKIP_NULL;
        return $this;
    }

    public function getSkipNull(): bool {
        return ($this->flags & self::SKIP_NULL) != 0;
    }

    /**
     * Define to skip empty arrays.
     * @param boolean $value
     * @return static
     */
    public function setSkipEmptyArray(bool $value = true): static {
        $this->flags = $value ? $this->flags | self::SKIP_EMPTY_ARRAY : $this->flags & ~self::SKIP_EMPTY_ARRAY;
        return $this;
    }

    public function getSkipEmptyArray(): bool {
        return ($this->flags & self::SKIP_EMPTY_ARRAY) != 0;
    }

    #endregion
}