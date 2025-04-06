<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../core/db.php';
require_once __DIR__ . '/../core/functions.php';

header('Content-Type: application/json');

$user_id = user('id');
$song_id = $_POST['song_id'] ?? null;

if ($user_id && $song_id) {
    // Kiểm tra xem bài hát đã được yêu thích chưa
    $exists = db_query_one("SELECT 1 FROM favorites WHERE user_id = :user_id AND song_id = :song_id LIMIT 1", [
        'user_id' => $user_id,
        'song_id' => $song_id
    ]);

    if (!$exists) {
        // Thêm bài hát vào danh sách yêu thích
        $insert_query = "INSERT INTO favorites (user_id, song_id, created_at) VALUES (:user_id, :song_id, CURRENT_TIMESTAMP)";
        $params = [
            'user_id' => $user_id,
            'song_id' => $song_id
        ];
        db_query($insert_query, $params);
        echo json_encode(['status' => 'success', 'action' => 'added', 'message' => 'Bài hát đã được thêm vào danh sách yêu thích']);
    } else {
        // Xóa bài hát khỏi danh sách yêu thích
        $delete_query = "DELETE FROM favorites WHERE user_id = :user_id AND song_id = :song_id";
        $params = [
            'user_id' => $user_id,
            'song_id' => $song_id
        ];
        db_query($delete_query, $params);
        echo json_encode(['status' => 'success', 'action' => 'removed', 'message' => 'Bài hát đã được xóa khỏi danh sách yêu thích']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID người dùng hoặc ID bài hát không hợp lệ']);
}
?>
