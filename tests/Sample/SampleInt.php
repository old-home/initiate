<?php

declare(strict_types=1);

namespace Graywings\Instantiate\Tests\Sample;

class SampleInt
{
    public function __construct(
        private(set) readonly int $value
    )
    {
    }
}
