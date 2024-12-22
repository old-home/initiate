<?php

declare(strict_types=1);

namespace Graywings\Instantiate\Tests\Sample;

class SampleIntersection
{
    public function __construct(private(set) readonly SampleInterface&SampleAbstract $value)
    {
    }
}
