<?php
namespace Pyther\Json;

use Pyther\Json\Attributes\Json as JsonAttribute;
use Pyther\Json\Attributes\JsonIgnore;
use Pyther\Json\Attributes\JsonType;

abstract class Meta
{
    /**
     * Return the array of meta arguments.
     *
     * @param \ReflectionProperty $property
     * @param string $metaName
     * @return array|null Return the array of arguments or null if the meta was nor found
     */
    public static function getPropertyMetaArguments(\ReflectionProperty $property, string $metaName): ?array
    {
        foreach ($property->getAttributes() as $attribute) {
            if ($attribute->getName() == $metaName) {
                return $attribute->getArguments();
            }
        }
        return null;
    }

    /**
     * Test if a meta property was given.
     *
     * @param \ReflectionProperty $property
     * @param string $metaName
     * @return boolean
     */
    public static function hasPropertyMeta(\ReflectionProperty $property, string $metaName): bool
    {
        foreach ($property->getAttributes() as $attribute) {
            if ($attribute->getName() == $metaName) {
                return true;
            }
        }
        return false;
    }

    public static function isExporIgnored(\ReflectionProperty $property): bool {
        $args = Meta::getPropertyMetaArguments($property, JsonIgnore::class);
        return $args !== null && (count($args) == 0 || $args[1] == true);
    }

    public static function isImportIgnored(\ReflectionProperty $property): bool {
        $args = Meta::getPropertyMetaArguments($property, JsonIgnore::class);
        return $args !== null && (count($args) < 2 || $args[1] == true);
    }

    public static function getPropertyMetaType(\ReflectionProperty $property): ?string {
        $args = Meta::getPropertyMetaArguments($property, JsonType::class);
        return $args !== null && count($args) > 0 ? $args[0] : null;
    }

}