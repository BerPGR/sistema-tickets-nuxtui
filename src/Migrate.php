<?php 

$sql = "
CREATE TABLE IF NOT EXISTS teams (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL UNIQUE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS clients (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL UNIQUE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  password_hash TEXT NOT NULL,
  role TEXT NOT NULL CHECK (role IN ('CHEFE', 'GERENTE', 'DESENVOLVEDOR', 'ANALISTA', 'REDATOR', 'COLUNISTA')),
  team_id INTEGER NOT NULL,
  created_at DATE DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (team_id) REFERENCES teams(id)
);

CREATE TABLE IF NOT EXISTS tickets (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  owner_id INTEGER NOT NULL,
  user_id INTEGER NOT NULL,
  title TEXT NOT NULL,
  description TEXT NOT NULL,
  due_date DATE,
  priority TEXT NOT NULL CHECK (priority IN ('ALTA', 'MEDIA', 'BAIXA')),
  status TEXT NOT NULL CHECK (status IN ('Aguardando', 'Em Execução', 'Revisão', 'Finalizado')) DEFAULT 'Aguardando',
  client_id INTEGER NOT NULL,
  team_id INTEGER NOT NULL,
  created_at DATE DEFAULT CURRENT_TIMESTAMP,
  updated_at DATE DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (client_id) REFERENCES clients(id),
  FOREIGN KEY (team_id) REFERENCES teams(id)
);

CREATE TABLE IF NOT EXISTS tags (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS ticket_tags (
  ticket_id INTEGER,
  tag_id INTEGER,
  PRIMARY KEY (ticket_id, tag_id),
  FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
  FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS ticket_updates (
  id INTEGER PRIMARY KEY AUTOINCREMENT, 
  ticket_id INTEGER NOT NULL,
  user_id INTEGER NOT NULL,
  modified_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  type TEXT CHECK (type IN ('comment', 'update', 'assignment')),
  comment TEXT
);
";

try {
  $config = Flight::get('config')['db'];

  $dsn = "{$config['driver']}:{$config['database']}";

  $pdo = new PDO($dsn);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->exec("PRAGMA foreign_keys = ON;");

  $pdo->exec($sql);
} catch (\PDOException $e) {
  die("Erro ao criar tabelas: " . $e->getMessage() . PHP_EOL);
} catch (\Throwable $e) {
  die("Erro de inicialização: " . $e->getMessage() . PHP_EOL);
}
