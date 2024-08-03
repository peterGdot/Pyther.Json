<?php
namespace Pyther\Json;

use Exception;
use Pyther\Json\Attributes\JsonDateTime;
use Pyther\Json\Exceptions\JsonException;

/**
 * @property JsonDeserializeSettings $settings
 */
class JsonDeserializer extends BaseExecuter
{
    function __construct(?JsonDeserializeSettings $settings = null)
    {
        parent::__construct($settings ?? new JsonDeserializeSettings());
    }

    /**
     * Deserialize a json string or a deserialized json array into a class object.
     *
     * @param string|array $jsonOrData The json string or its decoded version.
     * @param string|object $objectOrClass A class name or an already existing object of the class.
     * @return object|null Returns the deserialized object on success.
     */
    public function deserialize(string|array $jsonOrData, string|object $objectOrClass): ?object
    {
        $object = is_string($objectOrClass) ? static::createObject($objectOrClass) : $objectOrClass;
        try {
            $data = is_string($jsonOrData) ? json_decode($jsonOrData, true, 512, \JSON_INVALID_UTF8_IGNORE | \JSON_THROW_ON_ERROR) : $jsonOrData;
        } catch (Exception $ex) {
            throw new JsonException($ex->getMessage());
        }
        return $this->fillObject($object, $data);
    }

    /**
     * Deserialize an array of given objects.
     *
     * @param string|array $jsonOrData The json string or its decoded version.
     * @param string $itemClass The class name of the array elements.
     * @return array|null Returns the deserialized array on success.
     */
    public function deserializeArrayOf(string|array $jsonOrData, string $itemClass): ?array
    {
        try {
            $data = is_string($jsonOrData) ? json_decode($jsonOrData, true, 512, \JSON_INVALID_UTF8_IGNORE | \JSON_THROW_ON_ERROR) : $jsonOrData;
        } catch (Exception $ex) {
            throw new JsonException($ex->getMessage());
        }
        $result = [];
        foreach ($data as $item) {
            $object = static::createObject($itemClass);
            if (!is_array($item)) {
                throw new JsonException("Invalid Json. Array item is not a nested object!");
            }
            $result[] = $this->fillObject($object, $item);
        }
        return $result;
    }

    /**
     * Fill an object with given data (recursive).
     *
     * @param object $object
     * @param array $data
     * @param JsonSettings|null $settings
     * @return object|null
     */
    private function fillObject(object $object, array $data): ?object
    {
        static $ignoredTypes = ["callable", "false", "iterable", "never", "null", "true", "void"];

        $reflObject = new \ReflectionObject($object);

        $props = $reflObject->getProperties(
            \ReflectionProperty::IS_PUBLIC
            | ($this->settings->getIncludeProtected() ? \ReflectionProperty::IS_PROTECTED : 0)
        );

        foreach ($props as $prop)
        {
            if ($prop->isStatic()) continue;

            // #[JsonIgnore] => skip
            if (Meta::isDeserializeIgnored($prop)) continue;            

            // resolve name
            $name = $prop->getName();
            $jsonName = $this->resolveName($prop);

            // if no data available => skip;
            if (!isset($data[$jsonName])) continue;

            // gather type informations
            $typeInfo = new TypeInfo($prop);

            // ignored types => skip;
            if (in_array($typeInfo->type, $ignoredTypes)) {
                continue;
            }

            // a) special case: arrays
            if ($typeInfo->isArray) {
                if ($typeInfo->type === null) {
                    throw new JsonException("Can't figure out array data type!", $name);
                }
                $value = $object->{$name} ?? [];
                if (is_array($data[$jsonName])) {
                    foreach ($data[$jsonName] as $dataItem) {
                        $item = static::createObject($typeInfo->type, $reflObject->getNamespaceName());
                        $value[] = $this->fillObject($item, $dataItem);
                    }
                }
            }
            // b) special case: DateTime
            else if ($typeInfo->type == "DateTime") {
                $dateTimeMetaFormat = Meta::getPropertyMetaArguments($prop, JsonDateTime::class);
                $format = $dateTimeMetaFormat !== null ? $dateTimeMetaFormat[0] : $this->settings->getDateTimeFormat();
                $dateTime = \DateTime::createFromFormat($format, $data[$jsonName]);
                if ($dateTime === false) {
                    throw new JsonException("Invalid date/time format '$format'!", $name);
                }
                $value = $dateTime;
            }
            // c) special case: enum
            else if ($typeInfo->type !== null && enum_exists($typeInfo->type))
            {
                $value = static::getEnum($typeInfo->type, $data[$jsonName], $name);
            }
            // d) special case: nested objects
            else if ($typeInfo->type !== null && TypeInfo::isUserType($typeInfo->type)) {
                $item = static::createObject($typeInfo->type, $reflObject->getNamespaceName());
                $value = $this->fillObject($item, $data[$jsonName]);
            } 
            // e) default case
            else
            {
                $value = $data[$jsonName];
            }
            
            $this->setValue($object, $value, $prop);
        }

        return $object;
    }

    /**
     * Create a new object by full qualified class name
     * If the class wasn't found or doesn't offer an empty constructor or a constructor with default values only,
     * an JsonException is thrown.
     *
     * @param string $fqn The full qualified class name or the class name only if $ns was given.
     * @param string|null $ns The optional $ns if the full qualified was'n found.
     * @return object Returns a new instance of the object, on success.
     */
    private static function createObject(string $fqn, ?string $ns = null): object {        
        if (!class_exists($fqn) ) {
            if ($ns === null) {
                throw new JsonException("Class '$fqn' not found!");
            }
            $className = $fqn;
            $fqn = $ns."\\".$className;
            if (!class_exists($fqn)) {
                throw new JsonException("Class '$className' or '$fqn' not found!");
            }
        }
        try {
            return new $fqn();
        } catch (\ArgumentCountError $ex) {
            throw new JsonException("Class '$fqn' does not have an empty constructor or a constructor with default arguments only!");
        }
    }

    /**
     * Get a new enum based on enum class name and enum value.
     *
     * @param string $enumClass
     * @param mixed $value
     * @param string|null $propertyName
     * @return mixed
     */
    private function getEnum(string $enumClass, mixed $value, ?string $propertyName = null): mixed
    {
        if ($value === null) {
            return null;
        }
        if (is_array($value)) {
            $policy = $this->settings?->getNamingPolicy();
            $value = $value[$policy?->convert('name')] ?? $value[$policy?->convert('value')] ?? throw new JsonException("Value is not an Enumeration!", $propertyName);
        }
        try {
            $enumRefl = new \ReflectionEnum($enumClass);
            
            // find by name
            $result = $enumRefl->hasCase($value) ? $enumRefl->getCase($value)->getValue() : null;

            // find by value
            // tryFrom seems to be buggy :/
            // $result = $enumClass::tryFrom($value);
            if ($result === null && $enumRefl->isBacked()) {
                foreach ($enumRefl->getCases() as $case) {
                    if ($case->getBackingValue() == $value) {
                        return $case->getValue();
                    }
                }
            }
            return $result;
        } catch (Exception $ex) {
            throw new JsonException($ex->getMessage(), $propertyName);
        }
        
    }

}