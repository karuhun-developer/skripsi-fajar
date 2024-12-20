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

    public function createPppoeProfile($name, $localAddress, $remoteAddress, $rateLimit, $comment)
    {
        $query = new Query('/ppp/profile/add');
        $query->equal('name', $name);
        $query->equal('local-address', $localAddress);
        $query->equal('remote-address', $remoteAddress);
        $query->equal('rate-limit', $rateLimit);
        $query->equal('comment', $comment);

        return $this->client->query($query)->read();
    }

    public function updatePppoeProfile($id, $name, $localAddress, $remoteAddress, $rateLimit, $comment)
    {
        $query = new Query('/ppp/profile/set');
        $query->equal('.id', $id);
        $query->equal('name', $name);
        $query->equal('local-address', $localAddress);
        $query->equal('remote-address', $remoteAddress);
        $query->equal('rate-limit', $rateLimit);
        $query->equal('comment', $comment);

        return $this->client->query($query)->read();
    }

    public function deletePppoeProfile($id)
    {
        $query = new Query('/ppp/profile/remove');
        $query->equal('.id', $id);

        return $this->client->query($query)->read();
    }

    public function createPppoeSecret($name, $password, $service, $profile, $localAddress, $remoteAddress, $comment)
    {
        $query = new Query('/ppp/secret/add');
        $query->equal('name', $name);
        $query->equal('password', $password);
        $query->equal('service', $service);
        $query->equal('profile', $profile);
        $query->equal('local-address', $localAddress);
        $query->equal('remote-address', $remoteAddress);
        $query->equal('comment', $comment);

        return $this->client->query($query)->read();
    }

    public function updatePppoeSecret($id, $name, $password, $service, $profile, $localAddress, $remoteAddress, $comment)
    {
        $query = new Query('/ppp/secret/set');
        $query->equal('.id', $id);
        $query->equal('name', $name);
        $query->equal('password', $password);
        $query->equal('service', $service);
        $query->equal('profile', $profile);
        $query->equal('local-address', $localAddress);
        $query->equal('remote-address', $remoteAddress);
        $query->equal('comment', $comment);

        return $this->client->query($query)->read();
    }

    public function deletePppoeSecret($id)
    {
        $query = new Query('/ppp/secret/remove');
        $query->equal('.id', $id);

        return $this->client->query($query)->read();
    }
}
