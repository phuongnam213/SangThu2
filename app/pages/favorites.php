<?php
require_once __DIR__ . '/../core/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = user('id');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $song_id = $_POST['song_id'] ?? null;

    // Kiểm tra dữ liệu đầu vào
    if (!$song_id || !$user_id) {
        file_put_contents('debug.log', "Thiếu dữ liệu: user_id=$user_id, song_id=$song_id\n", FILE_APPEND);
        echo json_encode(['status' => 'error', 'message' => 'Thiếu dữ liệu song_id hoặc user_id']);
        exit;
    }

    // Kiểm tra bài hát đã được yêu thích chưa
    $exists = db_query_one("SELECT 1 FROM favorites WHERE user_id = :user_id AND song_id = :song_id", [
        'user_id' => $user_id,
        'song_id' => $song_id
    ]);

    if ($exists) {
        // Xóa bài hát khỏi favorites
        $result = db_query("DELETE FROM favorites WHERE user_id = :user_id AND song_id = :song_id", [
            'user_id' => $user_id,
            'song_id' => $song_id
        ]);
        if ($result) {
            file_put_contents('debug.log', "Xóa yêu thích: user_id=$user_id, song_id=$song_id\n", FILE_APPEND);
            echo json_encode(['status' => 'success', 'action' => 'removed']);
        } else {
            file_put_contents('debug.log', "Lỗi khi xóa yêu thích: user_id=$user_id, song_id=$song_id\n", FILE_APPEND);
            echo json_encode(['status' => 'error', 'message' => 'Không thể xóa yêu thích']);
        }
    } else {
        // Thêm bài hát vào favorites
        $result = db_query("INSERT INTO favorites (user_id, song_id) VALUES (:user_id, :song_id)", [
            'user_id' => $user_id,
            'song_id' => $song_id
        ]);
        if ($result) {
            file_put_contents('debug.log', "Thêm yêu thích: user_id=$user_id, song_id=$song_id\n", FILE_APPEND);
            echo json_encode(['status' => 'success', 'action' => 'added']);
        } else {
            file_put_contents('debug.log', "Lỗi khi thêm yêu thích: user_id=$user_id, song_id=$song_id\n", FILE_APPEND);
            echo json_encode(['status' => 'error', 'message' => 'Không thể thêm vào yêu thích']);
        }
    }
    exit;
}

// Kiểm tra nếu người dùng đã đăng nhập
// $user_id = $_SESSION['id'] ?? null;

// Nếu không phải yêu cầu AJAX, truy vấn và hiển thị danh sách bài hát yêu thích
require_once page('includes/header');
$query = "
    SELECT songs.id, songs.title, songs.artist_id, songs.slug, songs.image, songs.views, songs.date
    FROM favorites
    JOIN songs ON favorites.song_id = songs.id
    WHERE favorites.user_id = :user_id
    ORDER BY favorites.created_at DESC
";

$favorites = db_query($query, ['user_id' => $user_id]);
?>

<div class="favorites-container" style="max-width: 1000px; margin: 0 auto; padding: 20px;">
    <h2>Danh sách bài hát yêu thích của bạn</h2>
    <?php if (!empty($favorites)): ?>
        <?php foreach ($favorites as $song): ?>
            <div class="favorite-item" style="display: flex; align-items: center; padding: 15px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 15px; background-color: #f9f9f9;">
                <div class="favorite-image" style="width: 100px; height: 100px; overflow: hidden; border-radius: 8px; margin-right: 15px;">
                    <a href="<?= ROOT ?>/song/<?= htmlspecialchars($song['slug']) ?>">
                        <img src="<?= ROOT ?>/<?= htmlspecialchars($song['image']) ?>" alt="<?= htmlspecialchars($song['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    </a>
                </div>
                <div class="favorite-info" style="flex: 1;">
                    <div class="favorite-title" style="font-size: 20px; font-weight: bold;"><?= htmlspecialchars($song['title']) ?></div>
                    <div class="favorite-artist" style="font-size: 16px; color: #666;">by: <?= htmlspecialchars(get_artist($song['artist_id'])) ?></div>
                    <div style="margin-top: 5px;">Views: <?= $song['views'] ?></div>
                    <div>Date added: <?= get_date($song['date']) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Bạn chưa có bài hát nào trong danh sách yêu thích. Hãy bắt đầu thêm bài hát vào danh sách yêu thích!</p>
    <?php endif; ?>
</div>

<?php require_once page('includes/footer'); ?>



<!-- Đặt đoạn mã JavaScript ở đây -->

