<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'LOCH.php';

$app = new \Slim\App;

$isHttpsReverseProxied = $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https';
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

$app->run();
