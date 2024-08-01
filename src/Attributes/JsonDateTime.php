<?php
namespace Pyther\Json\Attributes;

#[\Attribute]
class JsonDateTime {
    public function __construct(public string $format) {}
}