<?php
namespace Demo\Models;

use Pyther\Json\Attributes\JsonType;

class OrderItem
{
    public string $sku;
        
    public ?OrderItem $parent = null;
}