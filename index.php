<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'LOCH.php';

$app = new \Slim\App;

$isHttpsReverseProxied = !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https';
$app->add(new \Slim\Middleware\HttpBasicAuthentication([
	'realm' => 'LOCH',
	'secure' => !$isHttpsReverseProxied,
	'users' => [
		HTTP_USER => HTTP_PASSWORD
	]
]));

$app->post('/api/add/csv', function (Request $request, Response $response)
{
	$loch = new LOCH();
	$loch->AddCsvData($request->getBody()->getContents());

	return $response;
});

$app->get('/api/get/{from}/{to}', function (Request $request, Response $response, $args)
{
	$loch = new LOCH();

	header('Content-Type: application/json');
	echo json_encode($loch->GetLocationPoints($args['from'], $args['to']));

	return $response;
});

$app->get('/', function (Request $request, Response $response)
{
	include 'mainpage.php';

	return $response;
});

$app->run();
