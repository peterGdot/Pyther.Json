<?php
namespace Pyther\Json\Attributes;

#[\Attribute]
class Json {
    public function __construct(public string $name) {}
}