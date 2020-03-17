<?php

namespace markroland\ArcGIS;

/**
 *
 * A PHP class for interacting with ArcGIS
 *
 * @author Mark Roland
 * @copyright 2015 Mark Roland
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/markroland/arcgis-php
 *
 **/
class ArcGIS
{

    /**
     * Client ID
     * @var string
     */
    private $client_id;

    /**
     * Secret
     * @var string
     */
    private $secret;

    /**
     * API Token
     * @var string
     */
    private $token;

    /**
     * A variable to hold debugging information
     * @var array
     */
    public $debug = array();

    /**
     * Class constructor
     *
     * @param string $token Merchant ID
     * @return null
     **/
    public function __construct($client_id, $secret)
    {
        if (isset($client_id) && isset($secret)) {

            $this->token = $this->generateToken($client_id, $secret);

            if (is_null($this->token)) {
                throw new \Exception('Token could not be generated');
            }

        } else {
            throw new \Exception('Client ID and Secret are required');
        }

    }

    /**
     * Send a HTTP request to the API
     *
     * @param string $http_method The HTTP method to be used (GET, POST, PUT, DELETE, etc.)
     * @param string $api_method The API method to be called
     * @param array $data Any data to be sent to the API
     * @return string The raw API response from ArcGIS
     **/
    private function sendRequest($http_method, $api_method, $data = null)
    {

        // Standard data
        $url = 'http://geocode.arcgis.com/arcgis/rest/services/World/GeocodeServer' . $api_method;

        // String assumed
        if (strcasecmp($http_method, 'GET') == 0 && !empty($data)) {
            $url .= '?' . $data;
        }

        // Debugging output
        $this->debug = array();
        $this->debug['HTTP Method'] = $http_method;
        $this->debug['Request URL'] = $url;

        // Create a cURL handle
        $ch = curl_init();

        // Set the request
        curl_setopt($ch, CURLOPT_URL, $url);

        // Save the response to a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Set Request type
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $http_method);

        // Send data
        if (strcasecmp($http_method, 'POST') == 0 && !empty($data)) {

            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            // Debugging output
            $this->debug['Posted Data'] = $data;

        }

        // Execute cURL request
        $http_response = curl_exec($ch);

        // Save CURL debugging info
        $this->debug['Curl Info'] = curl_getinfo($ch);

        // Close cURL handle
        curl_close($ch);

        // Return parsed response
        return $http_response;
    }

    /**
     * Generate a token
     * @param string $client_id An ArcGIS Client ID
     * @param string $secret An ArcGIS Secret
     * @param int $expiration The lifetime of the token in seconds.
     * @return null Token class property will be set on success
     **/
    private function generateToken($client_id, $secret, $expiration = 1440)
    {

        // TODO: Validate expiration input

        // Set request data
        $data = array(
            'f' => 'json',
            'client_id' => $client_id,
            'client_secret' => $secret,
            'grant_type' => 'client_credentials',
            'expiration' => $expiration,
        );

        // Make request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.arcgis.com/sharing/rest/oauth2/token/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $curl_response = curl_exec($ch);
        curl_close($ch);

        // Parse response
        $response = json_decode($curl_response);

        // Save token if response is successful
        if (isset($response->access_token)) {
            return $response->access_token;
        }

    }

    /**
     * Geocode a group of addresses
     * @param object $addresses An object as defined by the ArcGIS API
     * @param string $country Acceptable values include the full country name,
     *   the ISO 3166-1 2-digit country code, or the ISO 3166-1 3-digit country code.
     *   Defaults to empty string for compatibility with < v1.1.0
     * @param string $method The HTTP method to use for the ArcGIS request, defaults to GET; alternative is POST
     * @return string The HTTP response as returned by ArcGIS
     **/
    public function geocodeAddresses($addresses, $country = '', $method = 'GET')
    {

        $data = array(
            'token' => $this->token,
            'addresses' => json_encode($addresses),
            'sourceCountry' => $country,
            'f' => 'json'
        );

        $data = http_build_query($data);

        return $this->sendRequest($method, '/geocodeAddresses', $data);
    }

    /**
     * Reverse Geocode a Location to an Address
     * @param object $location An object as defined by the ArcGIS API
     * @param string $method The HTTP method to use for the ArcGIS request, defaults to GET; alternative is POST
     * @return string The HTTP response as returned by ArcGIS
     **/
    public function reverseGeocode($location, $method = 'GET')
    {

        $data = array(
            'token' => $this->token,
            'location' => json_encode($location),
            'sourceCountry' => $country,
            'f' => 'json'
        );

        $data = http_build_query($data);

        return $this->sendRequest($method, '/reverseGeocode', $data);
    }
}
