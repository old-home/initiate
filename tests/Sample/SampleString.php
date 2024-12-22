<?php

declare(strict_types=1);

namespace Graywings\Instantiate\Tests\Sample;

class SampleString
{
    public function __construct(
        private(set) readonly string $name,
    )
    {
    }
}
