<?php

namespace Tickets\Controllers;

use PDO;
use Flight;

class ClientController {
  public function __construct(private PDO $pdo) {}

  public function index() {
    try {
      $sql = "SELECT id, name FROM clients";
      $stmt = $this->pdo->query($sql);
      $clients = $stmt->fetchAll();

      Flight::json($clients, 200);
    } catch (PDOException $e) {
      Flight::json(['message' => 'Não foi possível buscar clientes: ' . $e->getMessage()], 400);
    }
  }
}
