<?php

namespace Sendpulse\MarketRestApi;

use Sendpulse\MarketRestApi\Contracts\ClientInterface;
use Sendpulse\MarketRestApi\Contracts\StorageInterface;
use Sendpulse\MarketRestApi\Exception\ClientException;

class Client implements ClientInterface
{
    /**
     * @var string
     */
    private $apiUrl = 'https://api.sendpulse.com';

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var StorageInterface
     */
    private $tokenStorage;

    /**
     * @param string $path
     * @param array $data
     * @param bool $useToken
     * @return array|null
     * @throws ClientException
     */
    public function get(string $path, array $data = [], bool $useToken = false): ?array
    {
        return $this->executeRequest($path, self::METHOD_GET, $data, $useToken);
    }

    /**
     * @param string $path
     * @param array $data
     * @param bool $useToken
     * @return array|null
     * @throws ClientException
     */
    public function post(string $path, array $data = [], bool $useToken = true): ?array
    {
        return $this->executeRequest($path, self::METHOD_POST, $data, $useToken);
    }

    /**
     * @param string $path
     * @param array $data
     * @param bool $useToken
     * @return array|null
     * @throws ClientException
     */
    public function put(string $path, array $data = [], bool $useToken = true): ?array
    {
        return $this->executeRequest($path, self::METHOD_POST, $data, $useToken);

    }

    /**
     * @param string $path
     * @param array $data
     * @param bool $useToken
     * @return array|null
     * @throws ClientException
     */
    public function patch(string $path, array $data = [], bool $useToken = true): ?array
    {
        return $this->executeRequest($path, self::METHOD_PATCH, $data, $useToken);

    }

    /**
     * @param string $path
     * @param array $data
     * @param bool $useToken
     * @return array|null
     * @throws ClientException
     */
    public function delete(string $path, array $data = [], bool $useToken = true): ?array
    {
        return $this->executeRequest($path, self::METHOD_DELETE, $data, $useToken);
    }

    /**
     * @param string $path
     * @param string $method
     * @param array $data
     * @param bool $useToken
     * @return array|null
     * @throws ClientException
     */
    public function executeRequest(
        string $path,
        string $method = self::METHOD_GET,
        array $data = [],
        bool $useToken = true
    ): ?array {
        $url = $this->apiUrl . self::PATH_DELIMITER . $path;
        $curl = curl_init();

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Expect:'
        ];

        if ($useToken) {
            $headers[] = 'Authorization: ' . self::TOKEN_TYPE_BEARER . ' ' . $this->getAccessToken();
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        switch ($method) {
            case self::METHOD_POST:
                curl_setopt($curl, CURLOPT_POST, count($data));
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case self::METHOD_PUT:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, self::METHOD_PUT);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case self::METHOD_PATCH:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, self::METHOD_PATCH);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case self::METHOD_DELETE:
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, self::METHOD_DELETE);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            default:
                if (!empty($data)) {
                    $url .= '?' . http_build_query($data);
                }
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 300);
        curl_setopt($curl, CURLOPT_TIMEOUT, 300);

        $response = curl_exec($curl);
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $responseBody = json_decode(substr($response, $headerSize), true);
        $headers = substr($response, 0, $headerSize);
        $error = curl_error($curl);

        curl_close($curl);

        if ($httpCode >= 400) {
            if ($httpCode === 401) {
                $this->getNewAccessToken();
                $responseBody = $this->executeRequest($path, $method, $data);
            } else {
                throw new ClientException('Request ' . $method . ' ' . $url . ' failed!', $httpCode, $responseBody, $headers,
                    $error);
            }
        }

        return empty($responseBody) ? null : $responseBody;
    }

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @param StorageInterface $tokenStorage
     * @return $this
     */
    public function setClientCredentials(string $clientId, string $clientSecret, StorageInterface $tokenStorage): Client
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->tokenStorage = $tokenStorage;

        return $this;
    }

    /**
     * @param string $code
     * @param string $appId
     * @param string $appSecret
     * @return array|null
     * @throws ClientException
     */
    public function getClientCredentialsByCode(string $code, string $appId, string $appSecret): ?array
    {
        return $this->post('market-service/oauth/authorize', [
            'app_id' => $appId,
            'secret' => $appSecret,
            'code' => $code
        ], false)['data'] ?? null;
    }

    /**
     * @return string|null
     * @throws ClientException
     */
    private function getAccessToken(): ?string
    {
        return $this->tokenStorage->get($this->clientId) ?? $this->getNewAccessToken();
    }

    /**
     * @return string|null
     * @throws ClientException
     */
    private function getNewAccessToken(): ?string
    {
        $response = $this->post('oauth/access_token/market', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => self::GRANT_TYPE_CLIENT_CREDENTIALS
        ], false)['access_token'] ?? null;

        if (empty($response['access_token'])) {
            return null;
        }

        $accessToken = $response['access_token'];
        $expiresIn = $response['expires_in'] ?? 0;

        return $this->tokenStorage->set($this->clientId, (string)$accessToken, (int)$expiresIn)
            ? $response['access_token']
            : null;
    }
}
