<?php

declare(strict_types=1);

namespace App\Tests;

use Closure;
use Zenstruck\Browser\Json;

readonly class ProviderClass
{
    public function __construct(
        private Closure $requestData,
        private int $expectedStatus,
        private Closure $responseAssertions,
    ) {
    }

    /**
     * Get the request data.
     *
     * @return array<string, mixed>
     */
    public function getRequestData(): array
    {
        return ($this->requestData)();
    }

    /**
     * Get the expected status code.
     */
    public function getExpectedStatus(): int
    {
        return $this->expectedStatus;
    }

    /**
     * Run assertions on the response.
     */
    public function assertResponse(Json $response): void
    {
        ($this->responseAssertions)($response);
    }
}
