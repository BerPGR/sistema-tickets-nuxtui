<?php

namespace Tickets\Controllers;
use Flight;
use PDO;

class TicketController {
  public function __construct(private PDO $pdo) {}

  public function save() {
    $data = Flight::request()->data;
    
  }
}
