<?php

declare(strict_types=1);

namespace Graywings\Instantiate\Tests\Sample;

class SampleParent
{
    public function __construct(private(set) readonly SampleChild $value)
    {
    }
}
