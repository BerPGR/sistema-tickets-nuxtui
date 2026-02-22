<?php

namespace Tickets\Controllers;

use PDO;
use Flight;

class TeamController {
  public function __construct(private PDO $pdo) {}
 
  public function index() {
    try {
      $sql = "SELECT id, name FROM teams";
      $stmt = $this->pdo->query($sql);
      $teams = $stmt->fetchAll();

      Flight::json($teams, 200);
    } catch (PDOException $e) {
      Flight::json('Erro ao pegar dados: ' . $e->getMessage(), 400);
    } catch (Throwable $e) {
      Flight::json('Erro geral: ' . $e->getMessage(), 400);
    }
  }
}
