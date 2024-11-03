<?php

declare(strict_types=1);

namespace Tests\Traits;

use BadMethodCallException;
use Psr\Container\ContainerInterface;
use UnexpectedValueException;

trait ContainerTestTrait
{
    protected ?ContainerInterface $container;

    protected function setUpContainer(ContainerInterface $container = null): void
    {
        if ($container instanceof ContainerInterface) {
            $this->container = $container;

            return;
        }

        throw new UnexpectedValueException('Container must be initialized');
    }

    protected function setContainerValue(string $name, mixed $value): void
    {
        if (isset($this->container) && method_exists($this->container, 'set')) {
            $this->container->set($name, $value);

            return;
        }

        throw new BadMethodCallException('This DI container does not support this feature');
    }
}
