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

// Nếu không phải yêu cầu AJAX, truy vấn và hiển thị danh sách bài hát yêu thích
require_once page('includes/header');
$query = "
    SELECT songs.id, songs.title, songs.artist_id, songs.category_id, songs.slug, songs.image, songs.views, songs.date
    FROM favorites
    JOIN songs ON favorites.song_id = songs.id
    WHERE favorites.user_id = :user_id
    ORDER BY favorites.created_at DESC
";

$favorites = db_query($query, ['user_id' => $user_id]);
?>

<div class="favorites-container">
    <div class="section-header">
        <h2 class="section-title">Your Favorites</h2>
        <p class="section-description">Music you love, all in one place</p>
    </div>

    <div class="favorites-content">
        <?php if (!empty($favorites)): ?>
            <div class="favorites-list">
                <?php foreach ($favorites as $song): ?>
                    <div class="favorite-item">
                        <div class="favorite-image">
                            <a href="<?= ROOT ?>/song/<?= htmlspecialchars($song['slug']) ?>">
                                <img src="<?= ROOT ?>/<?= htmlspecialchars($song['image']) ?>" alt="<?= htmlspecialchars($song['title']) ?>">
                                <div class="play-overlay">
                                    <div class="play-icon">
                                        <i class="fas fa-play"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="favorite-details">
                            <div class="favorite-info">
                                <h3 class="favorite-title">
                                    <a href="<?= ROOT ?>/song/<?= htmlspecialchars($song['slug']) ?>">
                                        <?= htmlspecialchars($song['title']) ?>
                                    </a>
                                </h3>
                                <div class="favorite-artist">
                                    <i class="fas fa-microphone-alt"></i>
                                    <?= htmlspecialchars(get_artist($song['artist_id'])) ?>
                                </div>
                            </div>
                            <div class="favorite-meta">
                                <div class="meta-item">
                                    <i class="fas fa-tag"></i>
                                    <?= htmlspecialchars(get_category($song['category_id'])) ?>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-eye"></i>
                                    <?= number_format($song['views']) ?> views
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <?= get_date($song['date']) ?>
                                </div>
                            </div>
                        </div>
                        <div class="favorite-actions">
                            <button class="remove-favorite" data-song-id="<?= $song['id'] ?>">
                                <i class="fas fa-heart-broken"></i>
                                <span>Remove</span>
                            </button>
                            <a href="<?= ROOT ?>/music-playing/<?= htmlspecialchars($song['slug']) ?>" class="play-favorite">
                                <i class="fas fa-play"></i>
                                <span>Play</span>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-favorites">
                <i class="far fa-heart"></i>
                <h3>Your favorites list is empty</h3>
                <p>Start adding songs to your favorites to see them here!</p>
                <a href="<?= ROOT ?>/music" class="explore-button">
                    <i class="fas fa-music"></i> Explore Music
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .favorites-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }
    
    .section-header {
        text-align: center;
        margin-bottom: 40px;
    }
    
    .section-title {
        font-size: 32px;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 10px;
        position: relative;
        display: inline-block;
    }
    
    .section-title:after {
        content: '';
        position: absolute;
        width: 60px;
        height: 3px;
        background: var(--gradient);
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
    }
    
    .section-description {
        font-size: 16px;
        color: #666;
        max-width: 600px;
        margin: 15px auto 0;
    }
    
    .favorites-content {
        background-color: white;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .favorites-list {
        padding: 10px;
    }
    
    .favorite-item {
        display: flex;
        padding: 20px;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.3s ease;
        align-items: center;
    }
    
    .favorite-item:last-child {
        border-bottom: none;
    }
    
    .favorite-item:hover {
        background-color: #f9f9f9;
    }
    
    .favorite-image {
        width: 120px;
        height: 120px;
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        margin-right: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .favorite-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .favorite-item:hover .favorite-image img {
        transform: scale(1.05);
    }
    
    .play-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .favorite-item:hover .play-overlay {
        opacity: 1;
    }
    
    .play-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        transform: scale(0.8);
        transition: all 0.3s ease;
    }
    
    .favorite-item:hover .play-icon {
        transform: scale(1);
    }
    
    .favorite-details {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .favorite-info {
        margin-bottom: 10px;
    }
    
    .favorite-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .favorite-title a {
        color: var(--dark-color);
        text-decoration: none;
        transition: color 0.3s ease;
    }
    
    .favorite-title a:hover {
        color: var(--primary-color);
    }
    
    .favorite-artist {
        font-size: 16px;
        color: var(--primary-color);
        display: flex;
        align-items: center;
    }
    
    .favorite-artist i {
        margin-right: 5px;
        font-size: 14px;
    }
    
    .favorite-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .meta-item {
        font-size: 14px;
        color: #777;
        display: flex;
        align-items: center;
    }
    
    .meta-item i {
        margin-right: 5px;
        color: var(--primary-color);
        opacity: 0.7;
    }
    
    .favorite-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-left: 20px;
    }
    
    .remove-favorite,
    .play-favorite {
        padding: 8px 15px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 110px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .remove-favorite {
        background-color: #f8f0f0;
        color: #d33;
        border: none;
    }
    
    .remove-favorite:hover {
        background-color: #ffebeb;
        transform: translateY(-2px);
    }
    
    .play-favorite {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
    }
    
    .play-favorite:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(106, 17, 203, 0.2);
    }
    
    .remove-favorite i,
    .play-favorite i {
        margin-right: 5px;
    }
    
    .no-favorites {
        text-align: center;
        padding: 60px 20px;
    }
    
    .no-favorites i {
        font-size: 64px;
        color: #ddd;
        margin-bottom: 20px;
    }
    
    .no-favorites h3 {
        font-size: 24px;
        color: #333;
        margin-bottom: 10px;
    }
    
    .no-favorites p {
        font-size: 16px;
        color: #666;
        margin-bottom: 30px;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .explore-button {
        display: inline-flex;
        align-items: center;
        padding: 12px 25px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        border-radius: 8px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .explore-button i {
        margin-right: 8px;
    }
    
    .explore-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(106, 17, 203, 0.3);
    }
    
    @media (max-width: 768px) {
        .favorite-item {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .favorite-image {
            width: 100%;
            height: 200px;
            margin-right: 0;
            margin-bottom: 15px;
        }
        
        .favorite-details {
            width: 100%;
            margin-bottom: 15px;
        }
        
        .favorite-actions {
            flex-direction: row;
            width: 100%;
            margin-left: 0;
        }
        
        .remove-favorite,
        .play-favorite {
            flex: 1;
        }
    }
</style>

<script>
    // JavaScript để xóa bài hát khỏi danh sách yêu thích mà không cần tải lại trang
    document.addEventListener('DOMContentLoaded', function() {
        const removeButtons = document.querySelectorAll('.remove-favorite');
        
        removeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const songId = this.getAttribute('data-song-id');
                const favoriteItem = this.closest('.favorite-item');
                
                // Gửi yêu cầu AJAX để xóa khỏi danh sách yêu thích
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '<?=ROOT?>/favorites', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        const response = JSON.parse(xhr.responseText);
                        
                        if (response.status === 'success' && response.action === 'removed') {
                            // Hiệu ứng fade out và xóa khỏi DOM
                            favoriteItem.style.opacity = '0';
                            favoriteItem.style.height = favoriteItem.offsetHeight + 'px';
                            favoriteItem.style.transition = 'opacity 0.5s ease, height 0.5s ease 0.5s';
                            
                            setTimeout(() => {
                                favoriteItem.style.height = '0';
                                favoriteItem.style.padding = '0';
                                favoriteItem.style.margin = '0';
                                favoriteItem.style.overflow = 'hidden';
                                
                                setTimeout(() => {
                                    favoriteItem.remove();
                                    
                                    // Kiểm tra nếu không còn bài hát yêu thích nào
                                    const remainingItems = document.querySelectorAll('.favorite-item');
                                    if (remainingItems.length === 0) {
                                        location.reload(); // Tải lại trang để hiển thị thông báo không có bài hát
                                    }
                                }, 500);
                            }, 500);
                        }
                    }
                };
                xhr.send('song_id=' + songId);
            });
        });
    });
</script>

<?php require_once page('includes/footer'); ?>