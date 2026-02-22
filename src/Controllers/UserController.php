<?php

namespace Tickets\Controllers;
use PDO;
use Flight;

class UserController {

  public function __construct(private PDO $db) {}

  public function getUsers() {
    try {
      $sql = "SELECT id, name, team_id FROM users";
      $stmt = $this->db->query($sql);
      $users = $stmt->fetchAll();

      return Flight::json($users, 200);
    } catch (\Exception $e) {
      var_dump($e);
    }
  }
}
