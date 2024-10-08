<?php
namespace Pyther\Json\NamingPolicies;

/**
 * Convert "PascalCase" to "camelCase".
 */
class PascalToCamelNamingPolicy extends BaseNamingPolicy
{
    public function convert(string $name): string {
        return strlen($name) > 0 ? (strtolower($name[0]).substr($name, 1)) : "";
    }
}