<?php
namespace Pyther\Json\Attributes;

#[\Attribute]
class JsonEnum {
    public const Full = "full";
    public const Name = "name";
    public const Value = "value";
    public function __construct(public string $format) {}
}