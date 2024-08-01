<?php
namespace Pyther\Json\NamingPolicies;

abstract class BaseNamingPolicy {

    /**
     * Converts the model's property name to the json element name using the given naming policy. 
     *
     * @param string $name
     * @return void
     */
    public abstract function convert(string $name): string;
}