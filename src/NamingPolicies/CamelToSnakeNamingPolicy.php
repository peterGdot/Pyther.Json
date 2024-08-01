<?php
namespace Pyther\Json\NamingPolicies;

class CamelToSnakeNamingPolicy extends BaseNamingPolicy
{
    public function convert(string $name): string {
        return strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $name));
    }
}