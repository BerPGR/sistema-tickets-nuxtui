<?php

use Tickets\Controllers\AuthController;
use Tickets\Controllers\UserController;
use Tickets\Controllers\TicketController;
use Tickets\Controllers\ClientController;
use Tickets\Controllers\TeamController;

Flight::group('/api', function () {
  Flight::route('OPTIONS /*', function() {
    Flight::halt(200);
  });
  Flight::route('GET /home', function () {
    Flight::json(['message' => 'Deu bom!'], 200);
  });

  Flight::route('GET /users', [UserController::class, 'getUsers']);
  Flight::route('GET /users/@userId/tickets', [TicketController::class, 'index']);
  Flight::route('GET /tickets/@ticketId', [TicketController::class, 'show']);

  Flight::route('POST /tickets', [TicketController::class, 'save']);
  Flight::route('POST /tickets/@id/tags', [TicketController::class, 'saveTags']);

  Flight::route('GET /clients', [ClientController::class, 'index']);

  Flight::route('GET /teams', [TeamController::class, 'index']);

  Flight::route('POST /auth/register', [AuthController::class, 'register']);
  Flight::route('POST /auth/login', [AuthController::class, 'login']);
});
