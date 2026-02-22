<?php 

$sql = "
use tickets;

CREATE TABLE IF NOT EXISTS teams (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS clients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL UNIQUE,
  create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('CHEFE', 'GERENTE', 'DESENVOLVEDOR', 'ANALISTA', 'REDATOR', 'COLUNISTA') NOT NULL,
  team_id INT NOT NULL,
  create_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_team_user FOREIGN KEY (team_id) REFERENCES teams(id)
);

CREATE TABLE IF NOT EXISTS tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  owner_id INT NOT NULL,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  due_date DATE,
  priority ENUM('ALTA', 'MEDIA', 'BAIXA') NOT NULL,
  client_id INT NOT NULL,
  team_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_owner_ticket FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_user_ticket FOREIGN KEY (user_id) REFERENCES users(id),
  CONSTRAINT fk_client_ticket FOREIGN KEY (client_id) REFERENCES clients(id),
  CONSTRAINT fk_team_ticket FOREIGN KEY (team_id) REFERENCES teams(id)
);

CREATE TABLE IF NOT EXISTS tags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(60) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS ticket_tags (
  ticket_id INT,
  tag_id INT,
  PRIMARY KEY (ticket_id, tag_id),
  FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
  FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);
";

try {
  $config = Flight::get('config')['db'];

  $dsn = "{$config['driver']}:host={$config['host']};port={$config['port']};dbname={$config['database']}";

  $pdo = new PDO($dsn, $config['username'], $config['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

  $pdo->exec($sql);
} catch (\PDOException $e) {
  die("Erro ao criar tabelas: " . $e->getMessage() . PHP_EOL);
} catch (\Throwable $e) {
  die("Erro de inicialização: " . $e->getMessage() . PHP_EOL);
}
