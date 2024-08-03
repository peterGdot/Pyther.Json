<?php
namespace Pyther\Json;

use Pyther\Json\NamingPolicies\BaseNamingPolicy;

/**
 * Json settings for serialization and deserialization.
 */
abstract class JsonSettings
{
    // flags
    private const INCLUDE_PROTECTED_FLAG = 1;
    
    private int $flags = 0;
    private ?BaseNamingPolicy $namingPolicy = null;
    private string $dataTimeFormat = "Y-m-d\TH:i:s+";

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
     * Set the default date time format.
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

}