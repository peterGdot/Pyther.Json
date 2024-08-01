<?php
namespace Pyther\Json\Attributes;

#[\Attribute]
class JsonIgnore {
    public function __construct(public bool $export = true, public bool $import = true) {}
}