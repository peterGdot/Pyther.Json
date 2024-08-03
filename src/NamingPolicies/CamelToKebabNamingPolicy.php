<?php
namespace Pyther\Json\NamingPolicies;

/**
 * Convert "camelCase" to "kebab-case".
 */
class CamelToKebabNamingPolicy extends BaseNamingPolicy
{
    public function convert(string $name): string {
        return strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '-$0', $name));
    }
}