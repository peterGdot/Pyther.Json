<?php
namespace Pyther\Json;

use JsonException;
use Pyther\Json\Attributes\JsonDateTime;
use Pyther\Json\Attributes\JsonEnum;
use Pyther\Json\Types\EnumFormat;

class JsonSerializer extends BaseExecuter 
{
    /**
     * Serialize an object or array the the encoded json version.
     *
     * @param object|array $objectOrArray The object or array to encode.
     * @return string|null Returns the json string version on success, null otherwise.
     */
    public function serialize(object|array $objectOrArray): ?string
    {
        if (is_array($objectOrArray)) {
            $data = $this->serializeArray($objectOrArray);
        } else {
            $data = $this->serializeObject((object)$objectOrArray);
        }
        try {
            return json_encode(
                $data, 
                \JSON_THROW_ON_ERROR
                | $this->settings->getPrettyPrint() ? \JSON_PRETTY_PRINT : 0
            );
        } catch (\Exception $ex) {
            throw new JsonException($ex->getMessage());
        }
    }

    /**
     * Serialize a given object.
     *
     * @param object $object The object to serialize.
     * @return array|null
     */
    private function serializeObject(object $object): ?array
    {
        $data = [];
        
        $reflObject = new \ReflectionObject($object);

        $props = $reflObject->getProperties(
            \ReflectionProperty::IS_PUBLIC
            | ($this->settings->getIncludeProtected() ? \ReflectionProperty::IS_PROTECTED : 0)
        );

        foreach ($props as $prop)
        {
            if ($prop->isStatic()) continue;

            // #[JsonIgnore] => skip
            if (Meta::isSerializeIgnored($prop)) continue;
            
            // resolve name
            $name = $prop->getName();
            $jsonName = $this->resolveName($prop);

            // gather type informations
            $typeInfo = new TypeInfo($prop);

            $value = $this->getValue($object, $prop);
            
            // skip properties with null values
            if ($value === null && $this->settings->getSkipNull()) continue;

            // skip inherited properties?
            if ($this->settings->getSkipInheritedProperties() && $prop->getDeclaringClass()->getName() != $reflObject->getName()) continue;

            if ($value === null) {
                $data[$jsonName] = null;
            }
            // a) special case: arrays
            else if ($typeInfo->isArray) {
                if (empty($value) && $this->settings->getSkipEmptyArray()) continue;
                $data[$jsonName] = $this->serializeArray($value);
            }
            // b) special case: DateTime
            else if ($typeInfo->type == "DateTime") {
                if ($this->settings->getDateTimeAsString()) {
                    $dateTimeMetaFormat = Meta::getPropertyMetaArguments($prop, JsonDateTime::class);
                    $format = $dateTimeMetaFormat !== null ? $dateTimeMetaFormat[0] : $this->settings->getDateTimeFormat();
                    $dateTimeFormated = $value->format($format);
                    $data[$jsonName] = $dateTimeFormated;
                } else {
                    $data[$jsonName] = $value;
                }
            }
            // c) special case: enum
            else if ($typeInfo->type !== null && enum_exists($typeInfo->type))
            {
                $data[$jsonName] = $this->serializeEnum($prop, $value, $typeInfo->type);
            }            
            // d) special case: nested objects
            else if ($typeInfo->type != null && TypeInfo::isUserType($typeInfo->type)) {
                $data[$jsonName] = $this->serializeObject($value);
            } 
            // e) default case
            else {
                $data[$jsonName] = $value;
            }
        }
        return $data;
    }

    /**
     * Serialize a given array.
     *
     * @param array $array The array to serialize.
     * @return array|null
     */
    private function serializeArray(array $array): ?array
    {
        $data = [];
        foreach ($array as $item) {
            if ($item instanceof \Datetime) {
                if ($this->settings->getDateTimeAsString()) {
                    $dateTimeFormated = $item->format($this->settings->getDateTimeFormat());
                    $data[] = $dateTimeFormated;
                } else {
                    $data[] = $item;
                }
            }
            else if (is_object($item)) {
                $data[] = $this->serializeObject($item);
            } else {
                $data[] = $item;
            }
        }
        return $data;
    }

    /**
     * Serialize a given enumeration value.
     *
     * @param \ReflectionProperty $property
     * @param mixed $value The enumeration value.
     * @param string $enumClass The enumeration full qualified class name.
     * @return mixed Returns the enumeration value.
     */
    private function serializeEnum(\ReflectionProperty $property, mixed $value, string $enumClass): mixed {
        if ($value === null) {
            return null;
        }
        $enumMeta = Meta::getPropertyMetaArguments($property, JsonEnum::class);
        $enumFormat = $enumMeta !== null ? $enumMeta[0] : $this->settings->getEnumFormat();
        if ($enumFormat === EnumFormat::Name) {
            return $value->name ?? $value;
        } else if ($enumFormat === EnumFormat::Full) {
            return $this->serializeObject($value);
        } else {
            $enumRefl = new \ReflectionEnum($enumClass);
            return $enumRefl->isBacked() ? $value : $value->name;
        }
    }
}