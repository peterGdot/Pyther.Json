<?php
namespace Demo\Models;

use Pyther\Json\Attributes\Json;
use Pyther\Json\Attributes\JsonIgnore;
use Pyther\Json\Attributes\JsonType;

class Order
{
    #[Json("Id")]
    public int $id2;

    public string|int|null $externId; 
 
    /**
     * @var string
     */
    public $channel;    

    #[JsonIgnore(true, true)]
    public string $ignore;

    /* @var OrderItem[] $items */
    #[JsonType(OrderItem::class)]
    public array $items = [];

    public OrderItem $primaryItem;
}