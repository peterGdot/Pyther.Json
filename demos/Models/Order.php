<?php
namespace Demo\Models;

use Pyther\Json\Attributes\Json;
use Pyther\Json\Attributes\JsonIgnore;
use Pyther\Json\Attributes\JsonType;

class Order
{
    #[Json("Id")]
    public int $id2 = 0;

    public string|int|null $externId = null; 
 
    /**
     * @var string
     */
    public $channel = "Demo Channel";    

    #[JsonIgnore(true, true)]
    public string $ignore;

    /* @var OrderItem[] $items */
    #[JsonType(OrderItem::class)]
    public array $items = [];

    public ?OrderItem $primaryItem = null;
}