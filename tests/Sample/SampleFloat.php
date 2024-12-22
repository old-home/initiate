<?php

declare(strict_types=1);

namespace Graywings\Instantiate\Tests\Sample;

class SampleFloat
{
    public function __construct(
        private(set) readonly float $depth
    )
    {
    }
}
