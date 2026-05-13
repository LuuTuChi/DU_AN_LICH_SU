<?php
// php/save_live.php
require_once 'config.php';
$db = getDB();
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) exit;

try {
    $stmt = $db->prepare("INSERT INTO live_sessions (title, topic, scheduled_at, meet_link, is_visible) 
                          VALUES (?, ?, ?, ?, 1)");
    $stmt->execute([
        $data['title'],
        $data['topic'],
        $data['scheduled_at'],
        $data['meet_link']
    ]);
    
    echo json_encode(['ok' => true]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}