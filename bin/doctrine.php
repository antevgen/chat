#!/usr/bin/env php
<?php

declare(strict_types=1);

use DI\Container;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

/** @var Container $container */
$bootstrap = require __DIR__ . '/../bootstrap/bootstrap.php';

ConsoleRunner::run(new SingleManagerProvider($bootstrap->getContainer()->get(EntityManagerInterface::class)));
