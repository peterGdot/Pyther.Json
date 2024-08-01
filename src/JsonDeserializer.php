<?php
namespace Pyther\Json;

use Pyther\Json\Attributes\JsonDateTime;
use Pyther\Json\Exceptions\JsonException;

class JsonDeserializer extends BaseExecuter
{
    function __construct(?JsonDeserializeSettings $settings = null)
    {
        parent::__construct($settings ?? new JsonDeserializeSettings());
    }

    public function deserialize(string|array $jsonOrData, string|object $objectOrClass): ?object
    {
        $object = is_string($objectOrClass) ? static::createObject($objectOrClass) : $objectOrClass;
        $data = is_string($jsonOrData) ? json_decode($jsonOrData, true, 512, \JSON_INVALID_UTF8_IGNORE) : $jsonOrData;
        return $this->fillObject($object, $data);
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
            | ($this->settings->includeProteced ? \ReflectionProperty::IS_PROTECTED : 0)
        );

        foreach ($props as $prop)
        {
            if ($prop->isStatic()) continue;

            // #[JsonIgnore] => skip
            if (Meta::isImportIgnored($prop)) continue;            

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
                if (is_array($data[$jsonName])) {
                    $object->{$name} ??= [];
                    foreach ($data[$jsonName] as $dataItem) {
                        $item = static::createObject($typeInfo->type, $reflObject->getNamespaceName());
                        $object->{$name}[] = $this->fillObject($item, $dataItem);
                    }
                }
            }
            // b) special case: DateTime
            else if ($typeInfo->type == "DateTime") {
                $dateTimeMetaFormat = Meta::getPropertyMetaArguments($prop, JsonDateTime::class);
                $format = $dateTimeMetaFormat !== null ? $dateTimeMetaFormat[0] : $this->settings->dataTimeFormat;
                $dateTime = \DateTime::createFromFormat($format, $data[$jsonName]);
                if ($dateTime === false) {
                    throw new JsonException("Invalid date/time format '$format'!", $name);
                }
                $object->{$name} = $dateTime;
            } 
            // c) special case: nested objects
            else if ($typeInfo->type != null && TypeInfo::isUserType($typeInfo->type)) {
                $item = static::createObject($typeInfo->type, $reflObject->getNamespaceName());
                $object->{$name} = $this->fillObject($item, $data[$jsonName]);
            } 
            // d) default case
            else {
                $object->{$name} = $data[$jsonName];
            }
        }

        return $object;
    }

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

}