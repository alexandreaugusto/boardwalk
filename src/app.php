<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Provider\MonologServiceProvider;

$app = new Silex\Application();

$app['debug'] = true;
$app['charset'] = "iso-8859-1";

$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__ . '/../logs/dev.log'
));

$app
    ->match('/calculator/add/{a}/{b}', 'Tutorial\Controller\Calculator::executeAdd')
    ->method('GET|POST');
$app
    ->match('/calculator/add/', 'Tutorial\Controller\Calculator::executeIndex')
    ->method('GET|POST');
/*$app
    ->match('/', 'Tutorial\Controller\Status::index')
    ->method('GET|POST');*/

$app
    ->match('/', function () use ($app) {
        $app['monolog']->addInfo('Logging example in the status route');

        return 'Running with log';
    })
    ->method('GET|POST');

return $app;