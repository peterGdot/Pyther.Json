<?php

namespace Demo\Models;

use DateTime;
use Pyther\Json\Attributes\JsonDateTime;
use Pyther\Json\Attributes\JsonType;

class TypesTest {    
    public static $staticProperty;

    // private types are ignored by default
    private $privateProperty;

    // protected types are not ignored by default
    protected $protectedProperty = 123;

    // public non type property
    public $publicProperty;

    public int $intProperty;

    public ?int $intPropertyNullable;

    public string $stringProperty;

    public ?string $stringPropertyNullable;

    public int|string $intOrStringProperty;

    public int|string|null $intOrStringPropertyNullable;

    public DateTime $dateTimeProperty;

    #[JsonDateTime("d/m/Y")]
    public DateTime $dateTimePropertyByMeta;

    public OrderItem $orderItem;

    public ?OrderItem $orderItemNullable;

    /**
     * @var OrderItem[]
     */
    public array $typedArrayByHint;

    #[JsonType(OrderItem::class)]
    public array $typedArrayByMeta;

    /**
     * @var integer
     */
    public $intByHint;

    /**
     * @var integer|null
     */
    public $intNullableByHint;
    

    #[JsonType(\int::class)]
    public $intByMeta;
}