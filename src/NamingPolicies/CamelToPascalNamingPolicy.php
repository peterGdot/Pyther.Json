<?php
namespace Pyther\Json\NamingPolicies;

class CamelToPascalNamingPolicy extends BaseNamingPolicy
{
    public function convert(string $name): string {
        return strlen($name) > 0 ? (strtoupper($name[0]).substr($name, 1)) : "";
    }
}