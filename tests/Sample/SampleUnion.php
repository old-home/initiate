<?php

declare(strict_types=1);

namespace Graywings\Instantiate\Tests\Sample;

class SampleUnion {
    public function __construct(private(set) readonly string|int $value)
    {
    }
}
