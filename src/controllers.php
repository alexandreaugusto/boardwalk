<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app
    ->match('/calculator/add/{a}/{b}', 'Tutorial\Controller\Calculator::executeAdd')
    ->method('GET|POST');
$app
    ->match('/calculator/add/', 'Tutorial\Controller\Calculator::executeIndex')
    ->method('GET|POST');
$app
    ->match('/', 'Tutorial\Controller\Status::index')
    ->method('GET|POST');