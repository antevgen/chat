<?php

declare(strict_types=1);

namespace Tests;

use Tests\Traits\CreatesApplicationTrait;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    use CreatesApplicationTrait;
}
