<?php

require __DIR__ . '/../vendor/autoload.php';

Tester\Environment::setup();

$configurator = new Nette\Configurator;
$configurator->setDebugMode(false);
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->addConfig(__DIR__ . '/../app/Config/config.neon');
$configurator->addConfig(__DIR__ . '/../app/Config/config.local.neon');
return $configurator->createContainer();
