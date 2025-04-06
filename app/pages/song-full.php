<?php
// Đảm bảo bạn đã kết nối đến database
require_once __DIR__ . '/../core/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập
$user_id = user('id');
if (!$user_id) {
    echo "Bạn cần đăng nhập để sử dụng tính năng này.";
    exit;
}

// Lấy thông tin bài hát
$row = $row ?? null;
if (!$row) {
    echo "Lỗi: Không tìm thấy bài hát.";
    exit;
}

$song_id = $row['id'] ?? null;
if (!$song_id) {
    echo "Lỗi: Không tìm thấy ID bài hát.";
    exit;
}

// Kiểm tra trạng thái yêu thích
$isFavorited = false;
$exists = db_query("SELECT 1 FROM favorites WHERE user_id = :user_id AND song_id = :song_id LIMIT 1", [
    'user_id' => $user_id,
    'song_id' => $song_id
]);
$isFavorited = !empty($exists);

// Tăng lượt xem bài hát
db_query("UPDATE songs SET views = views + 1 WHERE id = :id LIMIT 1", ['id' => $song_id]);

// Xử lý yêu cầu thêm/xóa bài hát khỏi danh sách yêu thích
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;
    $song_id = $_POST['song_id'] ?? null;

    if ($action === 'toggle_favorite' && $song_id) {
        $exists = db_query_one("SELECT 1 FROM favorites WHERE user_id = :user_id AND song_id = :song_id LIMIT 1", [
            'user_id' => $user_id,
            'song_id' => $song_id
        ]);

        if ($exists) {
            $delete_query = "DELETE FROM favorites WHERE user_id = :user_id AND song_id = :song_id";
            $params = ['user_id' => $user_id, 'song_id' => $song_id];
            db_query($delete_query, $params);
            $isFavorited = false;
        } else {
            $insert_query = "INSERT INTO favorites (user_id, song_id, created_at) VALUES (:user_id, :song_id, CURRENT_TIMESTAMP)";
            $params = ['user_id' => $user_id, 'song_id' => $song_id];
            db_query($insert_query, $params);
            $isFavorited = true;
        }

        // Trả về kết quả dưới dạng JSON
        echo json_encode(['status' => 'success', 'isFavorited' => $isFavorited]);
        exit;
    }
}
?>

<div class="music-card-full" style="max-width: 800px;">
    <h2 class="card-title"><?= esc($row['title']) ?></h2>
    <div class="card-subtitle">by: <?= esc(get_artist($row['artist_id'])) ?></div>

    <div class="img-song-full" style="overflow: hidden;">
        <a class="img-song" href="<?= ROOT ?>/song/<?= $row['slug'] ?>"><img src="<?= ROOT ?>/<?= $row['image'] ?>"></a>
    </div>
    <div class="card-content">
        <audio controls style="width: 100%">
            <source src="<?= ROOT ?>/<?= $row['file'] ?>" type="audio/mpeg">
        </audio>

        <div>Views: <?= $row['views'] ?></div>
        <div>Date added: <?= get_date($row['date']) ?></div>

        <div class="button-group">
            <!-- Nút yêu thích với trạng thái active dựa vào CSDL -->
            <button type="button" class="favorite-btn <?= $isFavorited ? 'active' : '' ?>" data-song-id="<?= $row['id'] ?>">
                <i class="fas fa-heart"></i> <?= $isFavorited ? 'Yêu thích' : 'Yêu thích' ?>
            </button>

            <a href="<?= ROOT ?>/download/<?= $row['slug'] ?>">
                <button class="download-btn">
                    Download
                </button>
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.favorite-btn').forEach(favoriteBtn => {
        favoriteBtn.addEventListener('click', function() {
            const songId = this.getAttribute('data-song-id');
            const btn = this;

            // Đổi màu ngay lập tức trước khi gửi yêu cầu
            btn.classList.toggle('active');
            btn.innerHTML = btn.classList.contains('active') ? '<i class="fas fa-heart"></i> Yêu thích' : '<i class="fas fa-heart"></i> Yêu thích';

            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'action': 'toggle_favorite',
                    'song_id': songId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status !== 'success') {
                    // Nếu có lỗi, hoàn tác thay đổi giao diện
                    btn.classList.toggle('active');
                    btn.innerHTML = btn.classList.contains('active') ? '<i class="fas fa-heart"></i> Yêu thích' : '<i class="fas fa-heart"></i> Yêu thích';
                    console.error('Error:', data.message);
                }
            })
            .catch(error => {
                // Nếu có lỗi mạng, hoàn tác thay đổi giao diện
                btn.classList.toggle('active');
                btn.innerHTML = btn.classList.contains('active') ? '<i class="fas fa-heart"></i> Yêu thích' : '<i class="fas fa-heart"></i> Yêu thích';
                console.error('Network error:', error);
            });
        });
    });
});
</script>

<style>
    .favorite-btn, .download-btn {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 18px;
        color: #000; /* Màu đen cho trạng thái chưa thêm yêu thích */
        transition: color 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px 20px;
        border-radius: 8px;
        margin-top: 15px;
    }

    .favorite-btn.active {
        color: #ff0000; /* Màu đỏ cho trạng thái đã thêm yêu thích */
    }

    .favorite-btn:hover, .download-btn:hover {
        color: #ff0000; /* Màu đỏ khi hover */
    }

    .download-btn {
        color: #6f42c1;
        background-color: #eaeaea;
    }

    .button-group {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 20px;
    }
</style>
