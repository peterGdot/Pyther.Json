<?php
namespace Pyther\Json;

use Pyther\Json\Attributes\Json as JsonAttribute;

/**
 * Base class for the serializer and deserializer class.
 */
class BaseExecuter
{
    protected ?JsonSettings $settings;

    function __construct(?JsonSettings $settings = null)
    {
        $this->settings = $settings;
    }
    
    /**
     * Rsolve the property name to its json equivalent based on "#[Json(...)]" meta/attribute or naming convention.
     *
     * @param \ReflectionProperty $property
     * @param JsonSettings|null $settings
     * @return string
     */
    protected function resolveName(\ReflectionProperty $property) : string
    {    
        // a) get name from #[Json("name")]
        $jsonMeta = Meta::getPropertyMetaArguments($property, JsonAttribute::class);
        if ($jsonMeta != null && strlen($jsonMeta[0] > 0)) {
            return $jsonMeta[0];
        }

        // b) get name from naming policity
        $name = $property->getName();
        return $this->settings?->naming?->convert($name) ?? $name;
    }
}