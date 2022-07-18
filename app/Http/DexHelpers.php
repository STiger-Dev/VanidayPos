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
        try {
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->dexUrl
            ]);
            $client->request("POST" ,"/erp/customers", ['body'=>json_encode($params)]);
        } catch (\Exception $e) {
            return;
        }
    }

    public function updateCustomer($id, $params) {
        try {
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->dexUrl
            ]);
            $client->request("PATCH" ,"/erp/customers/{$id}", ['body'=>json_encode($params)]);
        } catch (\Exception $e) {
            return;
        }
    }

    public function deleteCustomer($id) {
        try {
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->dexUrl
            ]);
            $client->request("DELETE" ,"/erp/customers/{$id}");
        } catch (\Exception $e) {
            return;
        }
    }

    public function addProduct($params) {
        try {
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->dexUrl
            ]);
            $client->request("POST" ,"/erp/product", ['body'=>json_encode($params)]);
        } catch (\Exception $e) {
            return;
        }
    }

    public function updateProduct($id, $params) {
        try {
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->dexUrl
            ]);
            $client->request("PATCH" ,"/erp/product/{$id}", ['body'=>json_encode($params)]);
        } catch (\Exception $e) {
            return;
        }
    }

    public function deleteProduct($id) {
        try {
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->dexUrl
            ]);
            $client->request("DELETE" ,"/erp/product/{$id}");
        } catch (\Exception $e) {
            return;
        }
    }

    public function addLocation($params) {
        try {
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->dexUrl
            ]);
            $client->request("POST" ,"/erp/location", ['body'=>json_encode($params)]);
        } catch (\Exception $e) {
            return;
        }
    }

    public function updateLocation($id, $params) {
        try {
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->dexUrl
            ]);
            $client->request("PATCH" ,"/erp/location/{$id}", ['body'=>json_encode($params)]);
        } catch (\Exception $e) {
            return;
        }
    }

    public function deleteLocation($id) {
        try {
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->dexUrl
            ]);
            $client->request("DELETE" ,"/erp/location/{$id}");
        } catch (\Exception $e) {
            return;
        }
    }

    public function addSales($params) {
        try {
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->dexUrl
            ]);
            $client->request("POST" ,"/erp/sales", ['body'=>json_encode($params)]);
        } catch (\Exception $e) {
            return;
        }
    }

    public function updateSales($id, $params) {
        try {
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->dexUrl
            ]);
            $client->request("PATCH" ,"/erp/sales/{$id}", ['body'=>json_encode($params)]);
        } catch (\Exception $e) {
            return;
        }
    }

    public function deleteSales($id) {
        try {
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->dexUrl
            ]);
            $client->request("DELETE" ,"/erp/sales/{$id}");
        } catch (\Exception $e) {
            return;
        }
    }

    public function addCategory($params) {
        try {
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->dexUrl
            ]);
            $client->request("POST" ,"/erp/category", ['body'=>json_encode($params)]);
        } catch (\Exception $e) {
            return;
        }
    }

    public function updateCategory($id, $params) {
        try {
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->dexUrl
            ]);
            $client->request("PATCH" ,"/erp/category/{$id}", ['body'=>json_encode($params)]);
        } catch (\Exception $e) {
            return;
        }
    }

    public function deleteCategory($id) {
        try {
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->dexUrl
            ]);
            $client->request("DELETE" ,"/erp/category/{$id}");
        } catch (\Exception $e) {
            return ;
        }
    }

    public function addEmployee($params) {
        try {
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->dexUrl
            ]);
            $client->request("POST" ,"/erp/employee", ['body'=>json_encode($params)]);
        } catch (\Exception $e) {
            return;
        }
    }

    public function deleteEmployee($id) {
        try {
            $client = new Client([
                // Base URI is used with relative requests
                'base_uri' => $this->dexUrl
            ]);
            $client->request("DELETE" ,"/erp/employee/{$id}");
        } catch (\Exception $e) {
            return;
        }
    }
}
