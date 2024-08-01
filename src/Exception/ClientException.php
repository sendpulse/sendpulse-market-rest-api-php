<?php

namespace Sendpulse\MarketRestApi\Exception;

use Sendpulse\MarketRestApi\Contracts\ClientExceptionInterface;
use Exception;
use Throwable;

class ClientException extends Exception implements ClientExceptionInterface
{
    /**
     * @var array
     */
    private $responseBody;

    /**
     * @var string|null
     */
    private $headers;

    /**
     * @var string|null
     */
    private $curlErrors;

    /**
     * @param string $message
     * @param int $code
     * @param array $responseBody
     * @param string|null $headers
     * @param string|null $curlErrors
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        array $responseBody = [],
        ?string $headers = null,
        ?string $curlErrors = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->responseBody = $responseBody;
        $this->headers = $headers;
        $this->curlErrors = $curlErrors;
    }

    /**
     * @return array|null
     */
    public function getResponseBody(): ?array
    {
        return $this->responseBody;
    }

    /**
     * @return string|null
     */
    public function getHeaders(): ?string
    {
        return $this->headers;
    }

    public function getCurlErrors(): ?string
    {
        return $this->curlErrors;
    }
}
