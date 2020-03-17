<?php

// Include ArcGIS class. This can also be done through Composer Autoloading
require_once(__DIR__ . './../src/ArcGIS.php');

// Set credentials
define('ARCGIS_CLIENT_ID', 'Insert Here');
define('ARCGIS_SECRET', 'Insert Here');

// Create ArcGIS object
try {
    $ArcGIS = new \markroland\ArcGIS\ArcGIS(ARCGIS_CLIENT_ID, ARCGIS_SECRET);
} catch (\Exception $e) {
    print($e->getMessage());
}

// Define input
$location = (object) array(
    "x" => (float) -52.95631849999995,
    "y" => (float)  47.00820300000004
);

// Geocode address(es)
$response = $ArcGIS->reverseGeocode($location);

// Parse response
$parsed_data = json_decode($response);

// Dump data
var_dump($parsed_data);
