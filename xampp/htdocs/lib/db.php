<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $cfg = app_config();
    $serverDsn = sprintf(
        'mysql:host=%s;port=%d;charset=utf8mb4',
        $cfg['db_host'],
        (int) $cfg['db_port']
    );
    try {
        $server = new PDO($serverDsn, $cfg['db_user'], $cfg['db_password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $server->exec(
            'CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '``', $cfg['db_name']) .
            '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
        );
    } catch (Throwable $e) {
        error_log('[db] Skipping database creation: ' . $e->getMessage());
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $cfg['db_host'],
        (int) $cfg['db_port'],
        $cfg['db_name']
    );
    $pdo = new PDO($dsn, $cfg['db_user'], $cfg['db_password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci',
    ]);
    return $pdo;
}

function ensure_schema(): void
{
    $pdo = db();
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS agents (
          id VARCHAR(32) PRIMARY KEY,
          name VARCHAR(80) NOT NULL,
          role VARCHAR(160) NOT NULL,
          tone VARCHAR(160) NOT NULL,
          color VARCHAR(16) NOT NULL,
          mark VARCHAR(8) NOT NULL
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS posts (
          id BIGINT AUTO_INCREMENT PRIMARY KEY,
          title VARCHAR(240) NOT NULL,
          body TEXT NOT NULL,
          author_id VARCHAR(32) NOT NULL,
          created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          cheer_count INT NOT NULL DEFAULT 0,
          praise_count INT NOT NULL DEFAULT 0,
          boo_count INT NOT NULL DEFAULT 0,
          FOREIGN KEY (author_id) REFERENCES agents(id)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS thread_lines (
          id BIGINT AUTO_INCREMENT PRIMARY KEY,
          post_id BIGINT NOT NULL,
          agent_id VARCHAR(32) NOT NULL,
          text TEXT NOT NULL,
          created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
          FOREIGN KEY (agent_id) REFERENCES agents(id)
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS comments (
          id BIGINT AUTO_INCREMENT PRIMARY KEY,
          post_id BIGINT NOT NULL,
          name VARCHAR(80) NOT NULL,
          text TEXT NOT NULL,
          created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS bot_runs (
          id BIGINT AUTO_INCREMENT PRIMARY KEY,
          kind VARCHAR(40) NOT NULL,
          note TEXT NOT NULL,
          created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
    ");
}
