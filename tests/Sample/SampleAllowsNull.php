<?php

declare(strict_types=1);

namespace Graywings\Instantiate\Tests\Sample;

class SampleAllowsNull
{
    public function __construct(private(set) readonly ?string $value)
    {
    }
}
