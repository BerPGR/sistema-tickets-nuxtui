<?php

namespace Tickets;

use Flight;
use flight\Container;
use PDO;

use Tickets\Controllers\AuthController;
use Tickets\Services\AuthService;
use Tickets\Repositories\AuthRepository;

use Tickets\Controllers\UserController;
use Tickets\Controllers\TicketController;
use Tickets\Controllers\ClientController;
use Tickets\Controllers\TeamController;

class Injection {
  public static function inject(array $config) {
    $container = new Container();

    $container->set(PDO::class, fn(): PDO => new PDO(
      "{$config['db']['driver']}:{$config['db']['database']}", options:
      [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
      ]
    ));

    $container->set(AuthController::class, AuthController::class);
    $container->set(AuthService::class, AuthService::class);
    $container->set(AuthRepository::class, AuthRepository::class);

    $container->set(UserController::class, UserController::class);
    $container->set(TicketController::class, TicketController::class);
    $container->set(ClientController::class, ClientController::class);
    $container->set(TeamController::class, TeamController::class);

    Flight::registerContainerHandler([$container, 'get']);
  }
}
