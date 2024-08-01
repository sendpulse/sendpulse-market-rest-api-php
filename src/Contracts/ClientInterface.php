<?php

namespace Sendpulse\MarketRestApi\Contracts;

interface ClientInterface
{
    public const METHOD_GET = 'GET';

    public const METHOD_POST = 'POST';

    public const METHOD_PUT = 'PUT';

    public const METHOD_PATCH = 'PATCH';

    public const METHOD_DELETE = 'DELETE';

    public const PATH_DELIMITER = '/';

    public const TOKEN_TYPE_BEARER = 'Bearer';

    public const GRANT_TYPE_CLIENT_CREDENTIALS = 'client_credentials';

    /**
     * @param string $path
     * @param array $data
     * @param bool $useToken
     * @return array|null
     */
    public function get(string $path, array $data = [], bool $useToken = false): ?array;

    /**
     * @param string $path
     * @param array $data
     * @param bool $useToken
     * @return array|null
     */
    public function post(string $path, array $data = [], bool $useToken = true): ?array;

    /**
     * @param string $path
     * @param array $data
     * @param bool $useToken
     * @return array|null
     */
    public function put(string $path, array $data = [], bool $useToken = true): ?array;

    /**
     * @param string $path
     * @param array $data
     * @param bool $useToken
     * @return array|null
     */
    public function patch(string $path, array $data = [], bool $useToken = true): ?array;

    /**
     * @param string $path
     * @param array $data
     * @param bool $useToken
     * @return array|null
     */
    public function delete(string $path, array $data = [], bool $useToken = true): ?array;

    /**
     * @param string $path
     * @param string $method
     * @param array $data
     * @param bool $useToken
     * @return array|null
     */
    public function executeRequest(
        string $path,
        string $method = self::METHOD_GET,
        array $data = [],
        bool $useToken = true
    ): ?array;

    /**
     * @param string $code
     * @param string $appId
     * @param string $appSecret
     * @return array|null
     */
    public function getClientCredentialsByCode(string $code, string $appId, string $appSecret): ?array;
}
