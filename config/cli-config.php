#!/usr/bin/env php
<?php

declare(strict_types=1);

use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\ORM\EntityManagerInterface;

require __DIR__ . '/../vendor/autoload.php';
$bootstrap = require __DIR__ . '/../bootstrap/bootstrap.php';

$config = new PhpFile(__DIR__ . '/../config/migrations.php'); // Customize this file path as needed
return DependencyFactory::fromEntityManager(
    $config,
    new ExistingEntityManager($bootstrap->getContainer()->get(EntityManagerInterface::class))
);
