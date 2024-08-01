<?php

namespace Sendpulse\MarketRestApi\Storage;

use Memcache;
use Sendpulse\MarketRestApi\Contracts\StorageInterface;

class MemcacheStorage implements StorageInterface
{
    /**
     * @var Memcache
     */
    protected $instance;

    /**
     * @param string $host
     * @param int $port
     * @param bool $persistent
     */
    public function __construct(string $host, int $port, bool $persistent = false)
    {
        $this->instance = new Memcache();

        if ($persistent) {
            $this->instance->pconnect($host, $port);
        } else {
            $this->instance->connect($host, $port);
        }
    }

    /**
     * @return Memcache
     */
    public function getInstance(): Memcache
    {
        return $this->instance;
    }

    /**
     * @param string $clientId
     * @param string $accessToken
     * @param int $expiresIn
     * @return bool
     */
    public function set(string $clientId, string $accessToken, int $expiresIn = 0): bool
    {
        return $this->instance->set($clientId, $accessToken, false, $expiresIn);
    }

    /**
     * @param string $clientId
     * @return string|null
     */
    public function get(string $clientId): ?string
    {
        $token = $this->instance->get($clientId);

        return empty($token) ? null : $token;
    }
}
