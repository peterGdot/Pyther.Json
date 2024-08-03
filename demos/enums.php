<?php
namespace Demo;

use JsonException;
use Pyther\Json\Json;
use Pyther\Json\JsonSerializeSettings;
use Pyther\Json\Attributes\JsonEnum;
use Pyther\Json\JsonDeserializeSettings;
use Pyther\Json\NamingPolicies\CamelToPascalNamingPolicy;
use Pyther\Json\Types\EnumFormat;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: text/plain");

$autoloader = require_once __DIR__."/../vendor/autoload.php";
$autoloader->addPsr4('Demo\\', __DIR__);

/**
 * A simple basic enumeration.
 */
enum Color {
    case Red;
    case Yellow;
    case Green;
}

/**
 * A simple backed enumeration.
 */
enum Status : int {
    case Inactive = 0;
    case Active = 1;
}

/**
 * Our test class.
 */
class EnumTest {
    // use settings
    public Status $statusDefault;

    #[JsonEnum(EnumFormat::Value)]
    public Status $statusValue;
    
    #[JsonEnum(EnumFormat::Name)]
    public Status $statusName;
    
    #[JsonEnum(EnumFormat::Full)]
    public Status $statusFull;

    #[JsonEnum(EnumFormat::Value)]
    public Color $redValue;

    #[JsonEnum(EnumFormat::Name)]
    public Color $yellowName;
    
    #[JsonEnum(EnumFormat::Full)]
    public Color $greenFull;
}

try {
    // test object
    $enumTest = new EnumTest();
    $enumTest->statusDefault = Status::Active;
    $enumTest->statusValue = Status::Active;
    $enumTest->statusName = Status::Active;
    $enumTest->statusFull = Status::Active;
    $enumTest->redValue = Color::Red;
    $enumTest->yellowName = Color::Yellow;
    $enumTest->greenFull = Color::Green;
    
    // serialize
    $json = serializeTest($enumTest);
    echo "JSON:\n";
    echo $json;

    // deserialze
    $obj = deserializeTest($json);
    echo "\n\nObject:\n";
    var_dump($obj);

} catch (JsonException $ex) {
    echo $ex->getMessage();
} catch (\Exception $ex) {
    echo "Internal Error: ".$ex->getMessage();
}

function serializeTest($object): string {
    $settings = new JsonSerializeSettings();
    $settings->setEnumFormat(EnumFormat::Name);
    $settings->setNamingPolicy(new CamelToPascalNamingPolicy());
    return Json::serialize($object, $settings);
}

function deserializeTest(string $json): EnumTest {
    $settings = new JsonDeserializeSettings();
    $settings->setNamingPolicy(new CamelToPascalNamingPolicy());
    return Json::deserialize($json, EnumTest::class, $settings);
}

