<?php
namespace Demo;

use Demo\Models\TypesTest;
use Pyther\Json\Exceptions\JsonException;
use Pyther\Json\Json;
use Pyther\Json\JsonDeserializeSettings;
use Pyther\Json\JsonSettings;
use Pyther\Json\NamingPolicies\CamelToKebabNamingPolicy;
use Pyther\Json\NamingPolicies\CamelToPascalNamingPolicy;
use Pyther\Json\NamingPolicies\CamelToSnakeNamingPolicy;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type: text/plain");

$autoloader = require_once __DIR__."/../vendor/autoload.php";
$autoloader->addPsr4('Demo\\', __DIR__);

try {
    $settings = new JsonDeserializeSettings();
    $settings->naming = new CamelToPascalNamingPolicy();
    // $settings->naming = new CamelToSnakeNamingPolicy();
    // $settings->naming = new CamelToKebabNamingPolicy();
    // $order = Json::deserialize(file_get_contents("data/order.json"), Order::class, $settings);
    $order = Json::deserialize(file_get_contents("data/types.json"), TypesTest::class);
    var_dump($order);
    $json = Json::serialize($order);
    file_put_contents("data/types2.json", $json);
    var_dump($json);
} catch (JsonException $ex) {
    echo $ex->getMessage();
/*} catch (JsonParsingException $ex) {
    echo "Json Parsing Error on property '".$ex->getPropertyName()."': ".$ex->getMessage();*/
} catch (\Exception $ex) {
    echo "Error: ".$ex->getMessage();
}