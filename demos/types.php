<?php
namespace Demo;

use Demo\Models\TypesTest;
use Pyther\Json\Exceptions\JsonException;
use Pyther\Json\Json;
use Pyther\Json\JsonDeserializeSettings;
use Pyther\Json\JsonSerializeSettings;
use Pyther\Json\NamingPolicies\CamelToPascalNamingPolicy;
use Pyther\Json\Types\EnumFormat;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type: text/plain");

$autoloader = require_once __DIR__."/../vendor/autoload.php";
$autoloader->addPsr4('Demo\\', __DIR__);

try {
    // deserialize (from file)
    $obj = deserializeTest(file_get_contents("data/types.json"));
    echo "Object:\n";
    var_dump($obj);

    // serialize
    $json = serializeTest($obj);
    echo "\n\nJSON:\n";
    echo $json;

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

function deserializeTest(string $json): TypesTest {
    $settings = new JsonDeserializeSettings();
    $settings->setNamingPolicy(new CamelToPascalNamingPolicy());
    return Json::deserialize($json, TypesTest::class, $settings);
}