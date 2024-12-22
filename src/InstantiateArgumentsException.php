<?php

declare(strict_types=1);

namespace Graywings\Instantiate;

use RuntimeException;

class InstantiateArgumentsException extends RuntimeException
{
    /**
     * @param array{
     *  name: string,
     *  value: mixed
     * }[] $errors
     */
    public function __construct(private(set) readonly array $errors)
    {
        var_dump($errors);
    }
}
