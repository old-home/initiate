<?php

declare(strict_types=1);

namespace Graywings\Instantiate\Tests\Sample;

use Graywings\Instantiate\PropertyName;

class SamplePropertyName {
    public function __construct(
        #[PropertyName('name')]
        private(set) readonly string $value
    )
    {
    }
}
