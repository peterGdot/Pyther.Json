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

    /**
     * Deserialize an array of given objects.
     *
     * @param string|array $jsonOrData The json string or its decoded version.
     * @param string $itemClass The class name of the array elements.
     * @param JsonSettings|null $settings Optional json settings.
     * @return array|null Return an array with populated object on success, null otherwise.
     */
    public static function deserializeArrayOf(string|array $jsonOrData, string $itemClass, ?JsonSettings $settings = null): ?array
    {
        return (new JsonDeserializer($settings))->deserializeArrayOf($jsonOrData, $itemClass);
    }

    /**
     * Serialize an object or array the the encoded json version.
     *
     * @param object|array $objectOrArray The object or array to encode.
     * @param JsonSettings|null $settings Optional json settings.
     * @return string|null Returns the json string version on success, null otherwise.
     */
    public static function serialize(object|array $objectOrArray, ?JsonSettings $settings = null): ?string
    {
        return (new JsonSerializer($settings))->serialize($objectOrArray);
    }
}