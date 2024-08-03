<?php
namespace Pyther\Json\Attributes;

use Pyther\Json\Types\EnumFormat;

/**
 * Attribute that allows overwriting individual enum formats.
 */
#[\Attribute]
class JsonEnum {
    public function __construct(public EnumFormat $format = EnumFormat::Value) {}
}