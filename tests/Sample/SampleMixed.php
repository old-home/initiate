<?php

declare(strict_types=1);

namespace Graywings\Instantiate\Tests\Sample;

class SampleMixed
{
    public function __construct(private(set) readonly mixed $value)
    {
    }
}
