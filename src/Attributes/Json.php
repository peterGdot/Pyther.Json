<?php
namespace Pyther\Json\Attributes;

/**
 * Attribute that allows overwriting individual property names.
 */
#[\Attribute]
class Json {
    public function __construct(public string $name) {}
}