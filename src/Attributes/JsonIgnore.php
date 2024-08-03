<?php
namespace Pyther\Json\Attributes;

/**
 * Attribute that allows overwriting individual serialize and/or deserialize usage.
 */
#[\Attribute]
class JsonIgnore {
    public function __construct(public bool $serialize = true, public bool $deserialize = true) {}
}