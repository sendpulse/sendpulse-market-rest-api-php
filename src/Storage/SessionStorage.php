<?php

namespace Sendpulse\MarketRestApi\Storage;

use Sendpulse\MarketRestApi\Contracts\StorageInterface;

class SessionStorage implements StorageInterface
{
    /**
     * @param string $clientId
     * @param string $accessToken
     * @param int $expiresIn
     * @return bool
     */
    public function set(string $clientId, string $accessToken, int $expiresIn = 0): bool
    {
        $_SESSION[$clientId] = $accessToken;

        return true;
    }

    /**
     * @param string $clientId
     * @return string|null
     */
    public function get(string $clientId): ?string
    {
        return empty($_SESSION[$clientId])
            ? null
            : (string)$_SESSION[$clientId];
    }
}
