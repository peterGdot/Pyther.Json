<?php
namespace Pyther\Json;

use Pyther\Json\Attributes\Json as JsonAttribute;

/**
 * Base class for the serializers and deserializers.
 */
abstract class BaseExecuter
{
    protected ?JsonSettings $settings;

    function __construct(?JsonSettings $settings = null)
    {
        $this->settings = $settings;
    }
    
    /**
     * Rsolve the property name to its json equivalent based on "#[Json(...)]" meta/attribute or settings naming convention.
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
        return $this->settings?->getNamingPolicy()?->convert($name) ?? $name;
    }

    /**
     * Get the object value by property (supports protected properties too).
     *
     * @param mixed $object The object to get the property value from. 
     * @param \ReflectionProperty $property The objects property.
     * @return mixed Returns the property value.
     */
    protected function getValue(mixed $object, \ReflectionProperty $property) : mixed {
        if ($property->isProtected()) {
            $property->setAccessible(true);
            return $property->getValue($object);
        } else {
            return $object->{$property->getName()};
        }
    }

    /**
     * Set the object value by property (supports protected properties too).
     *
     * @param mixed $object The object to set the property value to. 
     * @param [type] $value The value to set.
     * @param \ReflectionProperty $property The objects property.
     * @return void
     */
    protected function setValue(mixed $object, $value, \ReflectionProperty $property) {
        if ($property->isProtected()) {
            $property->setAccessible(true);
            $property->setValue($object, $value);
        } else {
            $object->{$property->getName()} = $value;
        }
    }
}