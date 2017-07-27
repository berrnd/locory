<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/data/config.php';
require_once __DIR__ . '/LOCH.php';
require_once __DIR__ . '/LochDbMigrator.php';
require_once __DIR__ . '/LochDemoDataGenerator.php';

$app = new \Slim\App;

if (PHP_SAPI !== 'cli')
{
	$app = new \Slim\App(new \Slim\Container([
		'settings' => [
			'displayErrorDetails' => true,
			'determineRouteBeforeAppMiddleware' => true
		],
	]));
	$container = $app->getContainer();
	$container['renderer'] = new PhpRenderer('./views');
}

if (PHP_SAPI === 'cli')
{
	$app->add(new \pavlakis\cli\CliRequest());
}

if (!LOCH::IsDemoInstallation())
{
	$sessionMiddleware = function(Request $request, Response $response, callable $next)
	{
		$route = $request->getAttribute('route');
		$routeName = $route->getName();

		if (!LOCH::IsValidSession($_COOKIE['loch_session']) && $routeName !== 'login')
		{
			$response = $response->withRedirect('/login');
		}
		else
		{
			$response = $next($request, $response);
		}

		return $response;
	};

	$app->add($sessionMiddleware);
}

$app->get('/login', function(Request $request, Response $response)
{
	return $this->renderer->render($response, '/layout.php', [
		'title' => 'Login',
		'contentPage' => 'login.php'
	]);
})->setName('login');

$app->post('/login', function(Request $request, Response $response)
{
	$postParams = $request->getParsedBody();
	if (isset($postParams['username']) && isset($postParams['password']))
	{
		if ($postParams['username'] === HTTP_USER && $postParams['password'] === HTTP_PASSWORD)
		{
			$sessionKey = LOCH::CreateSession();
			setcookie('loch_session', $sessionKey, time()+2592000); //30 days

			return $response->withRedirect('/');
		}
		else
		{
			return $response->withRedirect('/login?invalid=true');
		}
	}
	else
	{
		return $response->withRedirect('/login?invalid=true');
	}
})->setName('login');

$app->get('/logout', function(Request $request, Response $response)
{
	LOCH::RemoveSession($_COOKIE['loch_session']);
	return $response->withRedirect('/');
});

$app->get('/', function(Request $request, Response $response)
{
	LOCH::GetDbConnection(true); //For database schema migration

	return $this->renderer->render($response, '/layout.php', [
		'title' => 'Dashboard',
		'contentPage' => 'dashboard.php'
	]);
});

$app->group('/api', function()
{
	$this->post('/add/csv', function(Request $request, Response $response)
	{
		LOCH::AddCsvData($request->getBody()->getContents());
		echo json_encode(array('success' => true));
	});

	$this->get('/get/locationpoints/{from}/{to}', function(Request $request, Response $response, $args)
	{
		echo json_encode(LOCH::GetLocationPoints($args['from'] . ' 00:00:00', $args['to'] . ' 23:59:59'));
	});

	$this->get('/get/statistics/{from}/{to}', function(Request $request, Response $response, $args)
	{
		echo json_encode(LOCH::GetLocationPointStatistics($args['from'] . ' 00:00:00', $args['to'] . ' 23:59:59'));
	});
})->add(function($request, $response, $next)
{
	$response = $next($request, $response);
	return $response->withHeader('Content-Type', 'application/json');
});

$app->group('/cli', function()
{
	$this->get('/calculate/distance', function(Request $request, Response $response)
	{
		LOCH::CalculateLocationPointDistances();
	});

	$this->get('/recreatedemo', function(Request $request, Response $response)
	{
		if (LOCH::IsDemoInstallation())
		{
			LochDemoDataGenerator::RecreateDemo();
		}
	});
})->add(function($request, $response, $next)
{
	$response = $next($request, $response);

	if (PHP_SAPI !== 'cli')
	{
		echo 'Please call this only from CLI';
		return $response->withHeader('Content-Type', 'text/plain')->withStatus(400);
	}

	return $response->withHeader('Content-Type', 'text/plain');
});

$app->run();
