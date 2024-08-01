<?php
namespace Pyther\Json;

use Pyther\Json\Attributes\JsonDateTime;
use Pyther\Json\Attributes\JsonEnum;

/**
 * @property JsonSerializeSettings $settings
 */
class JsonSerializer extends BaseExecuter 
{
    function __construct(?JsonSerializeSettings $settings = null)
    {
        parent::__construct($settings ?? new JsonSerializeSettings());
    }

    public function serialize(object|array $objectOrArray): ?string
    {
        if (is_array($objectOrArray)) {
            $data = $this->serializeArray($objectOrArray);
        } else {
            $data = $this->serializeObject((object)$objectOrArray);
        }
        return json_encode($data, \JSON_PRETTY_PRINT);
    }

    private function serializeObject(object $object): ?array
    {
        $data = [];
        
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

            // gather type informations
            $typeInfo = new TypeInfo($prop);

            if ($object->{$name} === null) {
                $data[$jsonName] = null;
            }
            // a) special case: arrays
            else if ($typeInfo->isArray) {
                $data[$jsonName] = $this->serializeArray($object->{$name});
            }
            // b) special case: DateTime
            else if ($typeInfo->type == "DateTime") {
                $dateTimeMetaFormat = Meta::getPropertyMetaArguments($prop, JsonDateTime::class);
                $format = $dateTimeMetaFormat !== null ? $dateTimeMetaFormat[0] : $this->settings->dataTimeFormat;
                $dateTimeFormated = $object->{$name}->format($format);
                $data[$jsonName] = $dateTimeFormated;
            }
            // c) special case: enum
            else if ($typeInfo->type !== null && enum_exists($typeInfo->type))
            {
                $data[$jsonName] = $this->serializeEnum($prop, $object->{$name});
            }            
            // d) special case: nested objects
            else if ($typeInfo->type != null && TypeInfo::isUserType($typeInfo->type)) {
                $data[$jsonName] = $this->serializeObject($object->{$name});
            } 
            // e) default case
            else {
                $data[$jsonName] = $object->{$name};
            }
        }

        return $data;
    }

    private function serializeArray(array $array): ?array
    {
        $settings = $this->settings;

        $data = [];
        foreach ($array as $item) {
            if ($item instanceof \Datetime) {
                if ($settings->dateTimeAsString) {
                    $dateTimeFormated = $item->format($settings->dataTimeFormat);
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

    private function serializeEnum(\ReflectionProperty $property, $value) {
        if ($value === null) {
            return null;
        }
        $enumMeta = Meta::getPropertyMetaArguments($property, JsonEnum::class);
        $enumFormat = $enumMeta !== null ? $enumMeta[0] : ($this->settings->enumFormat ?? JsonEnum::Value);
        if ($enumFormat === JsonEnum::Name) {
            return $value->name ?? $value;
        } else if ($enumFormat === JsonEnum::Full) {
            return $this->serializeObject($value);
        } else {
            return $value;    
        }
    }
}