<?php

declare(strict_types=1);

namespace Tests\Traits;

trait ArrayTestTrait
{
    /**
     * @param array<array-key,mixed> $data
     */
    protected function getArrayValue(array $data, string $path, mixed $default = null): mixed
    {
        $currentValue = $data;
        $keyPaths = explode('.', $path);

        foreach ($keyPaths as $currentKey) {
            if (isset($currentValue->$currentKey)) {
                $currentValue = $currentValue->$currentKey;
                continue;
            }
            if (isset($currentValue[$currentKey])) {
                $currentValue = $currentValue[$currentKey];
                continue;
            }

            return $default;
        }

        // @phpstan-ignore-next-line
        return $currentValue === null ? $default : $currentValue;
    }
}
