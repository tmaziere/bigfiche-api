<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

require __DIR__.'/../config/prod.php';

$app->mount("/members", new \Bigfiche\Controller\Provider\Member());

$app->run();
