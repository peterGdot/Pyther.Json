<?php
namespace Pyther\Json\Attributes;

/**
 * Attribute that allows overwriting individual datetime formats.
 */
#[\Attribute]
class JsonDateTime {
    public function __construct(public string $format) {}
}