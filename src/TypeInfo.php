<?php
namespace Pyther\Json;

/**
 * Class thats parse and holds type informations.
 */
class TypeInfo {

    public bool $isNullable = false;
    public ?array $types = null;
    public ?string $type = null;
    public bool $isArray = false;

    /**
     * Create property informations.
     *
     * @param \ReflectionProperty $property
     */
    public function __construct(\ReflectionProperty $property) {
        $this->parse($property);
    }

    /**
     * Parse the given property.
     *
     * @param \ReflectionProperty $property
     * @return void
     */
    private function parse(\ReflectionProperty $property): void {
        // echo "Property '".$property->getName()."':\n";
        $metaTypes = Meta::getPropertyMetaType($property);
        if ($metaTypes !== null) {
            $this->types = explode("|", $metaTypes);
            if ($property->getType() == "array") {
                $this->isArray = true;
            }
        }
        if ($this->types === null) {
            $this->types = static::getTypes($property);
        }
        if ($this->types === null) {
            // type info by hint
            $typesByHint = static::getTypeFromHint($property);
            if ($typesByHint !== null) {
                $typesByHint = str_replace("integer", "int", $typesByHint);
                $this->types = explode("|", $typesByHint);
            }
        }
        if ($this->types !== null && count($this->types) == 1) {
            $this->type = $this->types[0];
        }
        // special case array        
        if ($this->type === "array") {
            $this->isArray = true;
            $this->type = static::getArrayType($property);
            $this->types = [$this->type];
        }

        // nullable
        if ($property->getType() !== null) {
            $this->isNullable = $property->getType()->allowsNull();
        }
        if (!$this->isNullable && $this->types != null) {
            $this->isNullable = in_array("null", $this->types);
        }
    }

    /**
     * Get the array type from hint or meta/attribute.
     *
     * @param \ReflectionProperty $property
     * @return string|null
     */
    private static function getArrayType(\ReflectionProperty $property): ?string {
        // get array type from meta
        $type = Meta::getPropertyMetaType($property);
        if ($type !== null) {
            return str_replace("[]", "", $type);
        }
        // get array type from data hint
        $type = static::getTypeFromHint($property);
        if ($type !== null) {
            return str_replace("[]", "", $type);
        }
        
        return null;
    }

    /**
     * Get property type informations from hints.
     *
     * @param \ReflectionProperty $property
     * @return string|null
     */
    private static function getTypeFromHint(\ReflectionProperty $property): ?string {
        if (preg_match('/@var\s+([^\s]+)/', $property->getDocComment(), $matches)) {
            list(, $type) = $matches;
            return $type != "" ? $type : null;
        }
        return null;
    }

    /**
     * Get an array of all types for a given property.
     *
     * @param \ReflectionProperty $property
     * @return array|null
     */
    private static function getTypes(\ReflectionProperty $property): ?array {
        $type = $property->getType();
        if ($type !== null) {            
            if ($type instanceof \ReflectionUnionType || $type instanceof \ReflectionIntersectionType) {
                $result = [];
                foreach ($type->getTypes() as $subType) {
                    $result[] = "".$subType;
                }
                return $result;
            } else if ($type instanceof \ReflectionNamedType) {
                return [ltrim("".$type, "?")];
            }
        }
        return null;
    }
    
    /**
     * Check, if the given type is a build in type.
     *
     * @param string $typeName
     * @return boolean
     */
    public static function isBuildInType(string $typeName): bool {
        static $buildInTypes = ["null", "bool", "int", "float", "string", "array", "object", "resource", "never", "void"];
        return in_array($typeName, $buildInTypes); 
    }

    /**
     * Check if the given type is a scalar type.
     *
     * @param string $typeName The type name to check against.
     * @return boolean
     */
    public static function isScalarType(string $typeName): bool {
        static $buildInTypes = ["bool", "int", "float", "string"];
        return in_array($typeName, $buildInTypes); 
    }

    /**
     * Check if the given type is a value type like "true" or "false".
     *
     * @param string $typeName The type name to check against.
     * @return boolean
     */
    public static function isValueType(string $typeName): bool {
        static $valueTypes = ["true", "false"];
        return in_array($typeName, $valueTypes); 
    }

    /**
     * Check if the given type is a callable type.
     *
     * @param string $typeName The type name to check against.
     * @return boolean
     */
    public static function isCallableType(string $typeName): bool {
        static $callableTypes = ["callable"];
        return in_array($typeName, $callableTypes); 
    }

    /**
     * Check if the given type is a user type like a class, interface, trait or enum.
     *
     * @param string $typeName The type name to check against.
     * @return boolean
     */
    public static function isUserType(string $typeName): bool {        
        return !static::isBuildInType($typeName) && !static::isCallableType($typeName);
    }

}