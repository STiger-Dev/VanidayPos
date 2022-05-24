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
}
