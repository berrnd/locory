<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'LOCH.php';

$app = new \Slim\App;

if (PHP_SAPI !== 'cli')
{
	$isHttpsReverseProxied = !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https';
	$app->add(new \Slim\Middleware\HttpBasicAuthentication([
		'realm' => 'LOCH',
		'secure' => !$isHttpsReverseProxied,
		'users' => [
			HTTP_USER => HTTP_PASSWORD
		]
	]));
}

if (PHP_SAPI === 'cli')
{
	$app->add(new \pavlakis\cli\CliRequest());
}

$app->post('/api/add/csv', function(Request $request, Response $response)
{
	$loch = new LOCH();

	$loch->AddCsvData($request->getBody()->getContents());

	return $response;
});

$app->get('/api/get/locationpoints/{from}/{to}', function(Request $request, Response $response, $args)
{
	$loch = new LOCH();

	echo json_encode($loch->GetLocationPoints($args['from'] . ' 00:00:00', $args['to'] . ' 23:59:59'));

	return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/api/get/statistics/{from}/{to}', function(Request $request, Response $response, $args)
{
	$loch = new LOCH();

	echo json_encode($loch->GetLocationPointStatistics($args['from'] . ' 00:00:00', $args['to'] . ' 23:59:59'));

	return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/cli/calculate/distance', function(Request $request, Response $response)
{
	if (PHP_SAPI !== 'cli')
	{
		echo 'Please call this only from CLI';
		return $response->withHeader('Content-Type', 'text/plain')->withStatus(400);
	}

	$loch = new LOCH();

	$loch->CalculateLocationPointDistances();

	return $response;
});

$app->get('/', function(Request $request, Response $response)
{
	include 'mainpage.php';

	return $response;
});

$app->run();
