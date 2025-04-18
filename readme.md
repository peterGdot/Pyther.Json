# Pyther.Json

A lightweight (de)serializer between json strings and data models with the following features:

- support for nested arrays and objects
- pre defined or custom naming policies
- support for basic or backed enumerations.
- meta/attribute support for
  - property renaming 
  - property exclusion
  - (array) data type
  - datetime format
  - enum format
- several settings like 
  - include protected properties
  - skip null values
  - skip empty arrays
  - skip inherited properties
  - enum format (name, value or full)
  - and more ...
- takes documentation "@var" hints into account
- no external dependencies  
- straightforward to use

Requirements:
- PHP 8.1+

## Installation
Install the [Composer Package](https://packagist.org/packages/pyther/json)

`composer require pyther/json`

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

### Optional Settings
```php
$settings = new JsonSettings();

// set optional naming policy, default is none (json attrbiutes equals object attribute names).
// supported policies: CamelToPascalNamingPolicy, CamelToSnakeNamingPolicy, CamelToKebabNamingPolicy, PascalToCamelNamingPolicy
$settings->setNamingPolicy(new CamelToPascalNamingPolicy());

// Disable json indention (enabled by default).
$settings->setPrettyPrint(false);

// Enable or disable to serialize DateTime as string (enabled by default).
$settings->setDateTimeAsString(false);

// Set the default date time format (\DateTime::W3C by default).
// This can be overwriten per Property using "#[JsonDateTime(...)]"
$settings->setDateTimeFormat(\DateTime::W3C)

// Defines the default serialization format for enumerations (EnumFormat::Value by default). 
// This can be overridden using the "JsonEnum" meta tag.
// values are: EnumFormat::Value, EnumFormat::Name, EnumFormat::Full
$settings->setEnumFormat(EnumFormat::Value)

// Define to skip null values (false by default).
$settings->setSkipNull(true);

// Define to skip empty arrays (false by defaut).
$settings->setSkipEmptyArray(true);

// Set to true, to include protected members (false by default).
$settings->setIncludeProtected(true)

// Define to skip inherited properties (false by default).
$settings->setSkipInheritedProperties(true);


$json = Json::serialize($order, $settings);

```

## Enums

```php
enum Status : int {
    case Inactive = 0;
    case Active = 1;
}

```

### EnumFormat::Name
```php
class EnumTest {
    #[JsonEnum(EnumFormat::Name)]
    public Status $status;
}

// or set global for all enums
$settings->setEnumFormat(EnumFormat::Name);
```
Result:
```json
{
    "Status": "Active"
}
```

### EnumFormat::Value
```php
class EnumTest {
    #[JsonEnum(EnumFormat::Value)]
    public Status $status;
}

// or set global for all enums
$settings->setEnumFormat(EnumFormat::Value);
```

Result:
```json
{
    "Status": 1
}
```

### EnumFormat::Full
```php
class EnumTest {
    #[JsonEnum(EnumFormat::Full)]
    public Status $status;
}

// or set global for all enums
$settings->setEnumFormat(EnumFormat::Full);
```

Result:
```json
{
    "Status": {
        "Name": "Active",
        "Value": 1
    }
}
```

## Meta/Attributes

### Json
The attribute allows to manually match a single property with a json attribute. This attribute will ignore the selected naming policy. 

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
Allows to ignore a single property during serialization, deserialization or both.

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
Defines a datatype for a single property. This is especially useful for arrays due the lack of type hint support for arrays in php.

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
Allows to define a date/time format for a single property.
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
Allows to define the enum serialization format for a single property.
```php
use Pyther\Json\Attributes\JsonEnum;

class MyClass
{
    // parse this property by the given format.
    #[JsonEnum(EnumFormat::Name)]
    public Status $status;
}
```

### JsonComplete
Allows a post process when the object and all child objects are ready.

```php
use Pyther\Json\Attributes\JsonComplete;

class MyClass
{
    // ...

    // Any parameterless function with any name you want. 
    #[JsonComplete]
    public function onComplete() {
        // $this is fully parsed and ready to use here
    }
}

```
