<?php
namespace Pyther\Json\Attributes;

#[\Attribute]
class JsonType {
    public function __construct(public ?string $type) {}
}