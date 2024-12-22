<?php

declare(strict_types=1);

namespace Graywings\Instantiate\Tests\Sample;

class SampleChild
{
    public function __construct(private(set) readonly int $value)
    {
    }
}
