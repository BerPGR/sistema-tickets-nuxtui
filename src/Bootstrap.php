<?php

use Dotenv\Dotenv;
use Tickets\CorsUtil;
use Tickets\Injection;

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->safeLoad();

$cors = new CorsUtil();
Flight::before('start', [$cors, 'handleCors']);

Flight::set('config', [
  'front_origin' => 'http://localhost:5173',
  'db' => [
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'tickets',
    'username' => 'root',
    'password' => 'MySqlUser270113!',
    'chartset' => 'utf8mb4'
  ]
]);

Flight::set('jwt.config', [
    'secret' => $_ENV['JWT_SECRET'],
    'iss' => $_ENV['JWT_ISS'],
    'aud' => $_ENV['JWT_AUD'],
    'ttl' => (int)$_ENV['JWT_AT_TTL']
]);

$config = Flight::get('config');

Flight::before('start', function () use ($config){
  Injection::inject($config);
});

Flight::map("error", function (Throwable $e) {
  Flight::response()->header('Access-Control-Allow-Origin', 'http://localhost:5173');
  Flight::response()->header('Access-Control-Allow-Credentials', 'true');
  $body = [
    'error' => true,
    'message' => $e->getMessage(),
    'type' => get_class($e),
    'file' => $e->getFile(),
    'line' => $e->getLine()
  ];
  Flight::json($body, 500);
});

Flight::map('notFound', function () {
  Flight::json(['error' => true, 'message' => 'Rota não encontrada'], 400);
});

require_once __DIR__ . '/Routes.php';
