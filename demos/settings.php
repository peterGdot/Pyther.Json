<?php
namespace Demo;

use Demo\Models\SpecialOrder;
use Pyther\Json\Exceptions\JsonException;
use Pyther\Json\Json;
use Pyther\Json\JsonSettings;
use Pyther\Json\NamingPolicies\CamelToPascalNamingPolicy;
use Pyther\Json\Types\EnumFormat;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Content-Type: text/plain");

$autoloader = require_once __DIR__."/../vendor/autoload.php";
$autoloader->addPsr4('Demo\\', __DIR__);

try {
    $order = new SpecialOrder();
    $order->dateTime = new \DateTime();

    $order = [ $order,  $order];
    // serialize with default settings
    $json = Json::serialize($order);
    echo "\n\nJSON without settings:\n";
    echo $json;

    // serialize with custom settings
    $settings = new JsonSettings();
    $settings->setNamingPolicy(new CamelToPascalNamingPolicy());
    $settings->setDateTimeAsString(false);
    $settings->setSkipNull(true);
    $settings->setSkipEmptyArray(true);
    $json = Json::serialize($order, $settings);

    echo "\n\nJSON with settings 1:\n";
    echo $json;

    // serialize with custom settings
    $settings->setSkipInheritedProperties(true);
    $json = Json::serialize($order, $settings);

    echo "\n\nJSON with settings 2:\n";
    echo $json;    

} catch (JsonException $ex) {
    echo $ex->getMessage();
} catch (\Exception $ex) {
    echo "Internal Error: ".$ex->getMessage();
}