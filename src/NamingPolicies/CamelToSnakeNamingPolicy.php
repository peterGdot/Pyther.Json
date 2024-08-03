<?php
namespace Pyther\Json\NamingPolicies;

/**
 * Convert "camelCase" to "snake_case".
 */
class CamelToSnakeNamingPolicy extends BaseNamingPolicy
{
    public function convert(string $name): string {
        return strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $name));
    }
}