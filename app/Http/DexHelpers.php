<?php

namespace App\Http;

use GuzzleHttp\Client;

class DexHelpers
{
    private $dexUrl;

    public function __construct()
    {
        $this->dexUrl = config('dex.base_url');
    }

    public function addCustomer($params) {
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->dexUrl
        ]);
        $client->request("POST" ,"/erp/customers", ['body'=>json_encode($params)]);
    }

    public function updateCustomer($id, $params) {
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->dexUrl
        ]);
        $client->request("PATCH" ,"/erp/customers/{$id}", ['body'=>json_encode($params)]);
    }

    public function deleteCustomer($id) {
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->dexUrl
        ]);
        $client->request("DELETE" ,"/erp/customers/{$id}");
    }

    public function addProduct($params) {
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->dexUrl
        ]);
        $client->request("POST" ,"/erp/product", ['body'=>json_encode($params)]);
    }

    public function updateProduct($id, $params) {
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->dexUrl
        ]);
        $client->request("PATCH" ,"/erp/product/{$id}", ['body'=>json_encode($params)]);
    }

    public function deleteProduct($id) {
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->dexUrl
        ]);
        $client->request("DELETE" ,"/erp/product/{$id}");
    }

    public function addLocation($params) {
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->dexUrl
        ]);
        $client->request("POST" ,"/erp/location", ['body'=>json_encode($params)]);
    }

    public function updateLocation($id, $params) {
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->dexUrl
        ]);
        $client->request("PATCH" ,"/erp/location/{$id}", ['body'=>json_encode($params)]);
    }

    public function deleteLocation($id) {
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->dexUrl
        ]);
        $client->request("DELETE" ,"/erp/location/{$id}");
    }

    public function addSales($params) {
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->dexUrl
        ]);
        $client->request("POST" ,"/erp/sales", ['body'=>json_encode($params)]);
    }

    public function updateSales($id, $params) {
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->dexUrl
        ]);
        $client->request("PATCH" ,"/erp/sales/{$id}", ['body'=>json_encode($params)]);
    }

    public function deleteSales($id) {
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->dexUrl
        ]);
        $client->request("DELETE" ,"/erp/sales/{$id}");
    }
}
