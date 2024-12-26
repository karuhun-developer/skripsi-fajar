<?php

namespace App\Services;

use App\Helper\MikrotikClient;
use RouterOS\Exceptions\BadCredentialsException;
use RouterOS\Exceptions\ClientException;
use RouterOS\Exceptions\ConfigException;
use RouterOS\Exceptions\ConnectException;
use RouterOS\Exceptions\QueryException;
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

    public static function connect($ipaddress, $username, $password, $port)
    {
        try {
            $client = new MikrotikClient([
                'host' => $ipaddress,
                'user' => $username,
                'pass' => $password,
                'port' => (int)$port,
            ]);
            return $client->connect();
        } catch (BadCredentialsException|ConnectException|ClientException|ConfigException|QueryException $e) {
            return false;
        }

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
