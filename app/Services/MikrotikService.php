<?php

namespace App\Services;

use RouterOS\Client;
use RouterOS\Query;
use App\Models\Router;

class MikrotikService
{
    protected $client;

    public function __construct($routerId)
    {
        $router = Router::find($routerId);
        $this->client = new Client([
            'host' => $router->ip_address,
            'user' => $router->username,
            'pass' => $router->password,
            'port' => $router->port,
        ]);
    }

    public function addPool($name, $description, $rangeIp)
    {
        $query = new Query('/ip/pool/add');
        $query->equal('name', $name);
        $query->equal('comment', $description);
        $query->equal('ranges', $rangeIp);

        return $this->client->query($query)->read();
    }

    public function updatePool($id, $name, $rangeIp, $description = "")
    {
        $query = new Query('/ip/pool/set');
        $query->equal('.id', $id);
        $query->equal('name', $name);
        $query->equal('ranges', $rangeIp);
        $query->equal('comment', $description);

        return $this->client->query($query)->read();
    }

    public function deletePool($id)
    {
        $query = new Query('/ip/pool/remove');
        $query->equal('.id', $id);

        return $this->client->query($query)->read();
    }
}
