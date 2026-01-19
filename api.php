<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

date_default_timezone_set('UTC');

$dbPath = '/var/www/douglasevaristo/coopy.sqlite';
$db = new PDO('sqlite:' . $dbPath);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function initTables(PDO $db): void
{
    $db->exec(
        "CREATE TABLE IF NOT EXISTS sessions (\n"
        . "  id INTEGER PRIMARY KEY AUTOINCREMENT,\n"
        . "  session_code TEXT NOT NULL,\n"
        . "  device_key TEXT NOT NULL,\n"
        . "  ip TEXT NOT NULL,\n"
        . "  created_at TEXT NOT NULL\n"
        . ");"
    );
    $db->exec("CREATE INDEX IF NOT EXISTS idx_sessions_code ON sessions(session_code);");

    $db->exec(
        "CREATE TABLE IF NOT EXISTS texts (\n"
        . "  id INTEGER PRIMARY KEY AUTOINCREMENT,\n"
        . "  session_code TEXT NOT NULL,\n"
        . "  device_key TEXT NOT NULL,\n"
        . "  text TEXT NOT NULL,\n"
        . "  kind TEXT NOT NULL DEFAULT 'message',\n"
        . "  created_at TEXT NOT NULL\n"
        . ");"
    );
    $db->exec("CREATE INDEX IF NOT EXISTS idx_texts_code ON texts(session_code);");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_texts_kind ON texts(kind);");
}

function jsonResponse(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function getIp(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function nowIso(): string
{
    return gmdate('Y-m-d H:i:s');
}

function generateDeviceKey(): string
{
    return bin2hex(random_bytes(8));
}

function generateSessionCode(PDO $db): string
{
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    for ($i = 0; $i < 20; $i++) {
        $code = '';
        for ($j = 0; $j < 4; $j++) {
            $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
        $stmt = $db->prepare('SELECT 1 FROM sessions WHERE session_code = ? LIMIT 1');
        $stmt->execute([$code]);
        if (!$stmt->fetchColumn()) {
            return $code;
        }
    }
    throw new RuntimeException('Nao foi possivel gerar codigo.');
}

function cleanupOld(PDO $db): void
{
    $db->exec("DELETE FROM texts WHERE created_at <= datetime('now', '-24 hours')");
    $db->exec("DELETE FROM sessions WHERE created_at <= datetime('now', '-24 hours')");
}

$db->exec('PRAGMA journal_mode = WAL;');
initTables($db);
$db->exec("PRAGMA foreign_keys = ON;");
cleanupOld($db);
$action = $_POST['action'] ?? $_GET['action'] ?? '';
$type = $_GET['type'] ?? '';

if ($type === 'cron') {
    cleanupOld($db);
    jsonResponse(['ok' => true, 'message' => 'limpeza executada']);
}

if ($action === 'create_session') {
    $sessionCode = generateSessionCode($db);
    $deviceKey = generateDeviceKey();
    $stmt = $db->prepare('INSERT INTO sessions (session_code, device_key, ip, created_at) VALUES (?, ?, ?, ?)');
    $stmt->execute([$sessionCode, $deviceKey, getIp(), nowIso()]);

    jsonResponse([
        'ok' => true,
        'session_code' => $sessionCode,
        'device_key' => $deviceKey,
    ]);
}

if ($action === 'join_session') {
    $sessionCode = strtoupper(trim($_POST['session_code'] ?? ''));
    if ($sessionCode === '') {
        jsonResponse(['ok' => false, 'error' => 'Codigo invalido.'], 400);
    }
    $stmt = $db->prepare('SELECT 1 FROM sessions WHERE session_code = ? LIMIT 1');
    $stmt->execute([$sessionCode]);
    if (!$stmt->fetchColumn()) {
        jsonResponse(['ok' => false, 'error' => 'Sessao nao encontrada.'], 404);
    }
    $deviceKey = $_POST['device_key'] ?? '';
    if ($deviceKey === '') {
        $deviceKey = generateDeviceKey();
    }
    $stmt = $db->prepare('INSERT INTO sessions (session_code, device_key, ip, created_at) VALUES (?, ?, ?, ?)');
    $stmt->execute([$sessionCode, $deviceKey, getIp(), nowIso()]);

    jsonResponse([
        'ok' => true,
        'session_code' => $sessionCode,
        'device_key' => $deviceKey,
    ]);
}

if ($action === 'send_message') {
    $sessionCode = strtoupper(trim($_POST['session_code'] ?? ''));
    $deviceKey = trim($_POST['device_key'] ?? '');
    $text = trim($_POST['text'] ?? '');
    if ($sessionCode === '' || $deviceKey === '' || $text === '') {
        jsonResponse(['ok' => false, 'error' => 'Dados incompletos.'], 400);
    }
    if (mb_strlen($text) > 5000) {
        jsonResponse(['ok' => false, 'error' => 'Texto muito grande.'], 413);
    }
    $stmt = $db->prepare('INSERT INTO texts (session_code, device_key, text, kind, created_at) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$sessionCode, $deviceKey, $text, 'message', nowIso()]);

    jsonResponse(['ok' => true]);
}

if ($action === 'send_live') {
    $sessionCode = strtoupper(trim($_POST['session_code'] ?? ''));
    $deviceKey = trim($_POST['device_key'] ?? '');
    $text = trim($_POST['text'] ?? '');
    if ($sessionCode === '' || $deviceKey === '') {
        jsonResponse(['ok' => false, 'error' => 'Dados incompletos.'], 400);
    }
    if (mb_strlen($text) > 5000) {
        jsonResponse(['ok' => false, 'error' => 'Texto muito grande.'], 413);
    }

    $stmt = $db->prepare('DELETE FROM texts WHERE session_code = ? AND device_key = ? AND kind = ?');
    $stmt->execute([$sessionCode, $deviceKey, 'live']);

    if ($text !== '') {
        $stmt = $db->prepare('INSERT INTO texts (session_code, device_key, text, kind, created_at) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$sessionCode, $deviceKey, $text, 'live', nowIso()]);
    }

    jsonResponse(['ok' => true]);
}

if ($action === 'fetch') {
    $sessionCode = strtoupper(trim($_POST['session_code'] ?? $_GET['session_code'] ?? ''));
    $deviceKey = trim($_POST['device_key'] ?? $_GET['device_key'] ?? '');
    $sinceId = (int)($_POST['since_id'] ?? $_GET['since_id'] ?? 0);

    if ($sessionCode === '') {
        jsonResponse(['ok' => false, 'error' => 'Sessao invalida.'], 400);
    }

    $stmt = $db->prepare('SELECT id, text, created_at FROM texts WHERE session_code = ? AND kind = ? AND id > ? ORDER BY id ASC');
    $stmt->execute([$sessionCode, 'message', $sinceId]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $db->prepare('SELECT text, created_at FROM texts WHERE session_code = ? AND kind = ? AND device_key != ? ORDER BY id DESC LIMIT 1');
    $stmt->execute([$sessionCode, 'live', $deviceKey]);
    $live = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

    $stmt = $db->prepare('SELECT MAX(id) FROM texts WHERE session_code = ? AND kind = ?');
    $stmt->execute([$sessionCode, 'message']);
    $lastId = (int)$stmt->fetchColumn();

    jsonResponse([
        'ok' => true,
        'messages' => $messages,
        'live' => $live,
        'last_id' => $lastId,
    ]);
}

jsonResponse(['ok' => false, 'error' => 'Acao invalida.'], 400);
