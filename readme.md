# Pyther.Json

A lightweight JSON (de)serializer between json and a data model with the following features:

- support for nested arrays and objects
- pre defined or custom naming policies
- meta/attribute support for
  - property exclusion
  - (array) type
  - datetime format
  - enum format
- takes documentation "@var" hints into account
- no external dependencies  

Requirements:
- PHP >= 8.1

## Examples:

### Deserialization
```php
    // creates a new order class and populate its properties from a json string or array. 
    $order = Json::deserialize($json, Order::class);
```

### Serialization
```php
    // creates a json string populate from orders properties.
    $json = Json::serialize($order);
```

## Meta/Attributes

### Json
The attribute allow two manually match a single property with a json attribute. This attribute will ignore the selected naming policy. 

```php
use Pyther\Json\Attributes\Json;

class MyClass
{
    // fill the "sku" from the json "id" property.    
    #[Json("id)]
    public string $sku;
}
```

### JsonIgnore
Allow to ignore a single property during serialization, deserialization or both.

```php
use Pyther\Json\Attributes\JsonIgnore;

class MyClass
{
    // ignore on serialization and deserialization
    #[JsonIgnore]
    public string $ignoreMe;

    // ignore on serialization only
    #[JsonIgnore(true, false)]
    public string $ignoreMe;

    // ignore on deserialization only
    #[JsonIgnore(false, true)]
    public string $ignoreMe;
}
```

### JsonType
Define a datatype for a single property. This is especially useful for arrays due the lack of type hint support for arrays in php.

```php
use Pyther\Json\Attributes\JsonType;

class MyClass
{
    // definfes the type of the array items.
    #[JsonType(OrderItem::class)]
    public array $typedArrayByMeta;

    // possible to replace the missing build in datatype.
    #[JsonType(\int::class)]
    public $intByMeta;
}
```

### JsonDateTime
Allow to define a date/time format for a single property.
```php
use Pyther\Json\Attributes\JsonDateTime;

class MyClass
{
    // parse this property by the given format.
    #[JsonDateTime("d/m/Y")]
    public string $dayOfBirth;
}
```

### JsonEnum
Allow to define the enum serialization format for a single property.
```php
use Pyther\Json\Attributes\JsonEnum;

class MyClass
{
    // parse this property by the given format.
    #[JsonEnum(JsonEnum::Name)]
    public Status $status;
}
```