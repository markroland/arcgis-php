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
$addresses = new stdClass();
$addresses->records = array(
    (object) array(
        'attributes' => (object) array(
            'OBJECTID' => (int) 1,
            'Address' => '1520 W 15th St',
            'City' => 'Lawrence',
            'Region' => 'KS',
            'Postal' => '66045'
        )
    )
);

// Geocode address(es)
$response = $ArcGIS->geocodeAddresses($addresses);

// Parse response
$parsed_data = json_decode($response);

// Dump data
var_dump($parsed_data);
