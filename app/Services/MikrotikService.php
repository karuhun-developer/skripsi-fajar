<?php

namespace App\Services;

use App\Helper\MikrotikClient;
use RouterOS\Query;
use App\Models\Router;

class MikrotikService
{
    protected $client;

    public function __construct($routerId)
    {
        $router = Router::find($routerId);
        $this->client = new MikrotikClient([
            'host' => $router->ip_address,
            'user' => $router->username,
            'pass' => $router->password,
            'port' => (int) $router->port,
        ]);
    }

    public function executeQuery($path, $params)
    {
        $query = new Query($path);
        foreach ($params as $key => $value) {
            $query->equal($key, $value);
        }
        return $this->client->query($query)->read();
    }
}
