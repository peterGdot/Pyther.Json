<?php
namespace Pyther\Json;

abstract class Json
{
    /**
     * Deserialize a json string or a deserialized json array into a class object.
     *
     * @param string|array $jsonOrData The json string or its decoded version.
     * @param string|object $objectOrClass A class name or an already existing object of the class.
     * @param JsonSettings|null $settings Optional json settings
     * @return object|null Return the populated object on success, null otherwise.
     */
    public static function deserialize(string|array $jsonOrData, string|object $objectOrClass, ?JsonSettings $settings = null): ?object
    {
        return (new JsonDeserializer($settings))->deserialize($jsonOrData, $objectOrClass);
    }

    public static function Serialize(object|array $objectOrArray, ?JsonSettings $settings = null): ?string
    {
        return (new JsonSerializer($settings))->serialize($objectOrArray);
    }
}