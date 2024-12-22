<?php

declare(strict_types=1);

namespace Graywings\Instantiate\Tests\Sample\SampleUser;

class SampleUser {
    public function __construct(
        private(set) readonly SampleUserId $id,
        private(set) readonly SampleUserEmail $email
    )
    {
    }
}
