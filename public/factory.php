<?php

require_once __DIR__ . '/../vendor/autoload.php';

$faker = Faker\Factory::create('pt-BR');
$pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=tickets", "root", "MySqlUser270113!");

$password = 'secret123';
$hash = password_hash($password, PASSWORD_BCRYPT);
$roles = array('CHEFE', 'GERENTE', 'DESENVOLVEDOR', 'ANALISTA', 'REDATOR', 'COLUNISTA');
$teams = array('Inteligência e imagem', 'Desenvolvimento', 'Coordenação', 'Infra e Suporte', 'Negócios', 'Mídia', 'Relacionamento');
$team_ids = [];
try {
  foreach($teams as $team) {
    $stmt = $pdo->prepare("INSERT INTO teams (name) VALUES (?)");
    $stmt->execute([$team]);

    $stmtId = $pdo->prepare("SELECT id FROM teams WHERE name = ?");
    $stmtId->execute([$team]);
    $team_ids[] = $stmtId->fetchColumn();
  }
} catch (PDOException $e) {
  die("Erro ao inserir equipes: " . $e->getMessage());
} catch (Throwable $e) {
  die("Erro padrão " . $e->getMessage());
}

try {
  $pdo->beginTransaction();
  $stmt = $pdo->prepare("INSERT INTO clients (name) VALUES (:name)");
  for ($j = 0; $j < 20; $j++) {
    $companyName = $faker->unique()->company;

    $stmt->execute([':name' => $companyName]);
  }
  $pdo->commit();
  echo 'Clientes Inseridos com sucesso';
} catch (PDOException $e) {
  $pdo->rollBack();
  die("Erro ao inserir clientes: " . $e->getMessage());
} catch (Throwable $e) {
  $pdo->rollBack();
  die('Erro geral ' . $e->getMessage());
}

for ($i = 0; $i < 30; $i++) 
{
  $randTeamId = $team_ids[array_rand($team_ids)]; 
  $role = $roles[array_rand($roles)];
  $sql = "INSERT INTO users (name, email, password_hash, role, team_id) VALUES (?, ?, ?, ?, ?)";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    $faker->name,
    $faker->unique()->safeEmail,
    $hash,
    $role,
    $randTeamId
  ]);
}
