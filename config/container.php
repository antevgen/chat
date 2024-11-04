<?php

declare(strict_types=1);

use App\Entity\Group;
use App\Entity\Message;
use App\Entity\User;
use App\Middleware\ExceptionMiddleware;
use App\Repository\GroupRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use App\Response\JsonResponse;
use App\Services\GroupService;
use App\Services\MessageService;
use App\Services\UserService;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Factory\AppFactory;

return [
    // Application settings
    'settings' => static fn () => require __DIR__ . '/settings.php',

    App::class => static function (ContainerInterface $container) {
        $app = AppFactory::createFromContainer($container);

        // Register routes
        (require __DIR__ . '/routes.php')($app);

        // Register middleware
        (require __DIR__ . '/middleware.php')($app);

        return $app;
    },

    ResponseFactoryInterface::class => static function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    ServerRequestFactoryInterface::class => static function (ContainerInterface $container) {
        return $container->get(Psr17Factory::class);
    },

    LoggerInterface::class => static function (ContainerInterface $container) {
        $settings = $container->get('settings')['logger'];
        $logger = new Logger('app');

        $filename = sprintf('%s/app.log', $settings['path']);
        $level = $settings['level'];
        $rotatingFileHandler = new RotatingFileHandler($filename, 0, $level, true, 0777);
        $rotatingFileHandler->setFormatter(new LineFormatter(null, null, false, true));
        $logger->pushHandler($rotatingFileHandler);

        return $logger;
    },

    ExceptionMiddleware::class => static function (ContainerInterface $container) {
        return new ExceptionMiddleware(
            $container->get(ResponseFactoryInterface::class),
            $container->get(JsonResponse::class),
            $container->get(LoggerInterface::class),
        );
    },

    EntityManagerInterface::class => static function (ContainerInterface $container) {
        $settings = $container->get('settings')['doctrine'];

        $config = ORMSetup::createAttributeMetadataConfiguration(
            $settings['metadata_dirs'],
            $settings['dev_mode'],
            $settings['cache_dir']
        );

        $dsnParser = new DsnParser();
        $params = $settings['connection']['url'] ? $dsnParser->parse($settings['connection']['url']) : $settings['connection'];

        // Configure the database connection
        $connection = DriverManager::getConnection($params, $config);

        // Create the EntityManager
        return new EntityManager($connection, $config);
    },

    UserRepository::class => static function (EntityManagerInterface $entityManager) {
        return $entityManager->getRepository(User::class);
    },

    UserService::class => static function (ContainerInterface $container) {
        return new UserService(
            $container->get(UserRepository::class),
        );
    },

    GroupRepository::class => static function (EntityManagerInterface $entityManager) {
        return $entityManager->getRepository(Group::class);
    },

    GroupService::class => static function (ContainerInterface $container) {
        return new GroupService(
            $container->get(EntityManagerInterface::class),
            $container->get(GroupRepository::class),
            $container->get(UserRepository::class),
        );
    },

    MessageRepository::class => static function (EntityManagerInterface $entityManager) {
        return $entityManager->getRepository(Message::class);
    },

    MessageService::class => static function (ContainerInterface $container) {
        return new MessageService(
            $container->get(MessageRepository::class),
            $container->get(GroupService::class)
        );
    },
];
