<?php

namespace Tickets\Controllers;
use Flight;
use PDO;

class TicketController {
  public function __construct(private PDO $pdo) {}

    public function index($userId) {
      try {
        $sql = "SELECT t.id as id, title, te.name as team, status, c.name as client, priority, due_date, t.created_at as created_at, u_owner.name as owner, u_user.name as responsable FROM tickets t
          INNER JOIN clients c ON c.id = t.client_id
          INNER JOIN teams te ON te.id = t.team_id
          INNER JOIN users u_owner ON t.owner_id = u_owner.id
          INNER JOIN users u_user ON  t.user_id = u_user.id
          WHERE t.owner_id = $userId";

        $stmt = $this->pdo->query($sql);
        $tickets = $stmt->fetchAll();

        Flight::json($tickets, 200);
      } catch (PDOException $e) {
        Flight::json('Erro ao consultar banco de dados: '. $e->getMessage(), 500);
      } catch (\Throwable $e) {
        Flight::json('Erro na função: ' . $e->getMessage(), 500);
      }
    }

    public function show(int $ticketId) {
      try {
        $sql = "SELECT t.id as id, title, te.name as team, status, c.name as client, priority, due_date, t.created_at as created_at, u_owner.name as owner, u_user.name as responsable,
          GROUP_CONCAT(tags.name SEPARATOR ', ') as tags
          FROM tickets t
          INNER JOIN clients c ON c.id = t.client_id
          INNER JOIN teams te ON te.id = t.team_id
          INNER JOIN users u_owner ON t.owner_id = u_owner.id
          INNER JOIN users u_user ON  t.user_id = u_user.id
          LEFT JOIN ticket_tags tt ON t.id = tt.ticket_id
          LEFT JOIN tags ON tt.tag_id = tags.id
          WHERE t.id = $ticketId
          GROUP BY t.id;
";

        $stmt = $this->pdo->query($sql);
        $ticket = $stmt->fetch();

        Flight::json($ticket, 200);

      } catch (PDOException $e) {
        Flight::json('Erro ao acessar banco: ' . $e->getMessage(), 500);
      } catch (\Throwable $e) {
        Flight::json("Erro ao mostrar ticket: " . $e->getMessage(), 500);
      }
    }

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
