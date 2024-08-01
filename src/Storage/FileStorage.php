<?php

namespace Sendpulse\MarketRestApi\Storage;

use Sendpulse\MarketRestApi\Contracts\StorageInterface;

class FileStorage implements StorageInterface
{
    /**
     * @var string
     */
    protected $storageFolder = '';

    /**
     * @param string $storageFolder
     */
    public function __construct(string $storageFolder)
    {
        $this->storageFolder = $storageFolder;
    }

    /**
     * @param string $clientId
     * @param string $accessToken
     * @param int $expiresIn
     * @return bool
     */
    public function set(string $clientId, string $accessToken, int $expiresIn = 0): bool
    {
        $tokenFile = fopen($this->storageFolder . $clientId, 'wb');
        fwrite($tokenFile, $accessToken);

        return fclose($tokenFile);
    }

    /**
     * @param string $clientId
     * @return string|null
     */
    public function get(string $clientId): ?string
    {
        $filePath = $this->storageFolder . $clientId;

        return file_exists($filePath) ? file_get_contents($filePath) : null;
    }
}
