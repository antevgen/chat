<?php

declare(strict_types=1);

namespace Tests\Traits;

use DI\Container;
use DI\ContainerBuilder;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

trait CreatesApplicationTrait
{
    use ArrayTestTrait;
    use ContainerTestTrait;
    use HttpTestTrait;
    use HttpJsonTestTrait;

    /**
     * @var App<Container>
     */
    protected App $app;

    /**
     * Before each test.
     */
    protected function setUp(): void
    {
        $container = (new ContainerBuilder())
            ->addDefinitions(__DIR__ . '/../../config/container.php')
            ->build();

        $this->app = $container->get(App::class);

        $this->setUpContainer($container);
    }
}
