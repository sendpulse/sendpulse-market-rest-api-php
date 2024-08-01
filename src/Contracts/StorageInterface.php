<?php

namespace Sendpulse\MarketRestApi\Contracts;

interface StorageInterface
{
    /**
     * @param string $clientId
     * @param string $accessToken
     * @param int $expiresIn
     * @return bool
     */
    public function set(string $clientId, string $accessToken, int $expiresIn = 0): bool;

    /**
     * @param string $clientId
     * @return string|null
     */
    public function get(string $clientId): ?string;
}
