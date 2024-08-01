<?php

namespace Sendpulse\MarketRestApi\Contracts;

interface ClientExceptionInterface
{
    /**
     * @return array|null
     */
    public function getResponseBody(): ?array;

    /**
     * @return string|null
     */
    public function getHeaders(): ?string;

    /**
     * @return string|null
     */
    public function getCurlErrors(): ?string;
}
