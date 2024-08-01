<?php
namespace Pyther\Json;

use Pyther\Json\NamingPolicies\BaseNamingPolicy;

/**
 * Json settings for serialization and deserialization.
 */
class JsonSettings {
    /**
     * The naming policy (1:1 by default).
     * 
     * @var BaseNamingPolicy|null
     */
    public ?BaseNamingPolicy $naming = null;

    /**
     * Set to true, to enable (de)serialization for proteced properties.
     *
     * @var boolean
     */
    public bool $includeProteced = false;

    /**
     * The default format for json date/time values.
     * This can be overridden per Property using "#[JsonDateTime(...)]"
     *
     * @var string
     */
    public string $dataTimeFormat = "Y-m-d\TH:i:s+";
}