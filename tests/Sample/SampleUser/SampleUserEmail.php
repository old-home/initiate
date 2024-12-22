<?php

declare(strict_types=1);

namespace Graywings\Instantiate\Tests\Sample\SampleUser;

class SampleUserEmail {
    public function __construct(
        private(set) readonly string $value
    )
    {
    }
}
