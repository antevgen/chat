<?php

declare(strict_types=1);

use DI\Container;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;

// Create the container and set up dependencies
$container = new Container();
AppFactory::setContainer($container);

// Load settings
$container->set('settings', require __DIR__ . '/../config/settings.php');

$container->set(EntityManagerInterface::class, function (ContainerInterface $container) {
    $settings = $container->get('settings');
    $doctrineSettings = $settings['settings']['doctrine'];

    $config = ORMSetup::createAttributeMetadataConfiguration(
        $doctrineSettings['metadata_dirs'],
        $doctrineSettings['dev_mode'],
        $doctrineSettings['cache_dir']
    );

    // Configure the database connection
    $connection = DriverManager::getConnection($doctrineSettings['connection'], $config);

    // Create the EntityManager
    return new EntityManager($connection, $config);
});

return $container;
