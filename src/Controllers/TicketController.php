<?php

namespace Tickets\Controllers;
use Flight;
use PDO;

class TicketController {
  public function __construct(private PDO $pdo) {}

    public function save() {
      try {
        $this->pdo->beginTransaction();

        $data = Flight::request()->data;
        $sql = "INSERT INTO tickets (owner_id, user_id, title, description, due_date, priority, client_id, team_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
          $data['owner_id'],
          $data['user_id'],
          $data['title'],
          $data['description'],
          $data['dueDate'],
          $data['priority'],
          $data['client_id'],
          $data['team_id']
        ]);
        
        $id = (int)$this->pdo->lastInsertId();
        $this->pdo->commit();

        Flight::json($id, 201); 

      } catch (\Throwable $e) {
        $this->pdo->rollBack();
        Flight::json('Erro ao criar ticket: ' . $e->getMessage(), 500);
      } catch (PDOException $e) {
        $this->pdo->rollBack();
        Flight::json('Erro PDO: ' . $e->getMessage(), 400);
      }
    }

  public function saveTags($id) {
    try {
      $allTags = Flight::request()->data;
      $ids = [];
      $this->pdo->beginTransaction();
      foreach($allTags as $tag) {
        $stmt = $this->pdo->prepare("INSERT INTO tags (name) VALUES (?)");
        $stmt->execute([
          $tag
        ]);

        $stmtId = $this->pdo->prepare("SELECT id FROM tags WHERE name = ?");
        $stmtId->execute([$tag]);
        $ids[] = $stmtId->fetchColumn();
      }
      $this->pdo->commit();

      $this->pdo->beginTransaction();
      foreach($ids as $i) {
        $sqlTicketTag = "INSERT INTO ticket_tags (ticket_id, tag_id) VALUES (?, ?)";
        $stmtTT = $this->pdo->prepare($sqlTicketTag);
        $stmtTT->execute([
          $id,
          $i
        ]);
      }
      $this->pdo->commit();

      Flight::json("Tags criadas com sucesso", 200);
    } catch (PDOException $e) {
      $this->pdo->rollBack();
      Flight::json('Erro ao criar tag: ' . $e->getMessage(), 400);
    } catch (\Throwable $e) {
      $this->pdo->rollBack();
      Flight::json('ERRO: ' . $e->getMessage(), 400);
    }
  }
}
