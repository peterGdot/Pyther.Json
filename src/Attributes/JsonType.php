<?php
namespace Pyther\Json\Attributes;

/**
 * Attribute that allows overwriting individual property types. This is especially useful for array types.
 */
#[\Attribute]
class JsonType
{
    public function __construct(public ?string $type) {}
}