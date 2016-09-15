<?php

class ArcGISTest extends PHPUnit_Framework_TestCase
{

    protected $ArcgisClient;

    public function setup()
    {

        try {
            $this->ArcgisClient = new \markroland\ArcGIS\ArcGIS(
                ARCGIS_CLIENT_ID,
                ARCGIS_SECRET
            );
        } catch (\Exception $e) {
            $caught = $e->getMessage();
        }

        $this->assertSame('Token could not be generated', $caught);
    }

    public function test_geocodeAddresses()
    {

        $addresses = (object) array(
            'records' => array(
                (object) array(
                    'attributes' => (object) array(
                        'OBJECTID' => (int) 1,
                        'Address' => '1600 Pennsylvania Ave NW',
                        'Region' => 'Washington, DC',
                        'Postal' => '20500',
                        'Country' => 'US'
                    )
                )
            )
        );

        if (isset($this->ArcgisClient)) {

            $response = $this->ArcgisClient->geocodeAddresses($addresses);

            $this->assertSame(
                '1600 Pennsylvania Ave NW, Washington, District of Columbia, 20500',
                json_decode($response)->locations[0]->address
            );
        }
    }
}
