<?php
// Lấy slug từ URL
$slug = $URL[1] ?? null;

// Truy vấn lấy thông tin bài hát từ bảng 'songs'
$query = "SELECT * FROM songs WHERE slug = :slug LIMIT 1";
$row = db_query_one($query, ['slug' => $slug]);

// Kiểm tra nếu không tìm thấy bài hát
if (!$row) {
    echo "Song not found.";
    exit;
}

// Đảm bảo bạn đã kết nối đến database
require_once __DIR__ . '/../core/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập
$user_id = user('id');
$song_id = $row['id'] ?? null;

// Tăng lượt xem bài hát
db_query("UPDATE songs SET views = views + 1 WHERE id = :id LIMIT 1", ['id' => $song_id]);

// Kiểm tra trạng thái yêu thích nếu đã đăng nhập
$isFavorited = false;
if ($user_id) {
    $exists = db_query("SELECT 1 FROM favorites WHERE user_id = :user_id AND song_id = :song_id LIMIT 1", [
        'user_id' => $user_id,
        'song_id' => $song_id
    ]);
    $isFavorited = !empty($exists);
}

// Xử lý yêu cầu thêm/xóa bài hát khỏi danh sách yêu thích
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'] ?? null;
    $post_song_id = $_POST['song_id'] ?? null;

    if ($action === 'toggle_favorite' && $post_song_id && $user_id) {
        $exists = db_query_one("SELECT 1 FROM favorites WHERE user_id = :user_id AND song_id = :song_id LIMIT 1", [
            'user_id' => $user_id,
            'song_id' => $post_song_id
        ]);

        if ($exists) {
            $delete_query = "DELETE FROM favorites WHERE user_id = :user_id AND song_id = :song_id";
            $params = ['user_id' => $user_id, 'song_id' => $post_song_id];
            db_query($delete_query, $params);
            $isFavorited = false;
        } else {
            $insert_query = "INSERT INTO favorites (user_id, song_id, created_at) VALUES (:user_id, :song_id, CURRENT_TIMESTAMP)";
            $params = ['user_id' => $user_id, 'song_id' => $post_song_id];
            db_query($insert_query, $params);
            $isFavorited = true;
        }

        // Trả về kết quả dưới dạng JSON
        echo json_encode(['status' => 'success', 'isFavorited' => $isFavorited]);
        exit;
    }
}

// Xử lý khi người dùng gửi bình luận
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    if (logged_in()) { // Kiểm tra xem người dùng có đăng nhập không
        $user_name = trim($_POST['user_name']);
        $comment = trim($_POST['comment']);

        if (!empty($user_name) && !empty($comment)) {
            $query = "INSERT INTO comments (song_id, user_name, comment) VALUES (:song_id, :user_name, :comment)";
            db_query($query, ['song_id' => $song_id, 'user_name' => $user_name, 'comment' => $comment]);

            // Refresh lại trang sau khi gửi bình luận
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }
    } else {
        $error_message = "You must be logged in to comment.";
    }
}
?>

<?php require page('includes/header') ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
    /* Song Page Styles */
    .music-page-container {
        max-width: 1000px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .page-title {
        text-align: center;
        margin-bottom: 40px;
        position: relative;
    }

    .page-title h1 {
        font-size: 32px;
        font-weight: 700;
        color: var(--dark-color);
        display: inline-block;
    }

    .page-title h1:after {
        content: '';
        position: absolute;
        width: 60px;
        height: 3px;
        background: var(--gradient);
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
    }

    .no-music-found {
        text-align: center;
        padding: 50px 0;
    }

    .no-music-found i {
        font-size: 60px;
        color: #ddd;
        margin-bottom: 20px;
    }

    .no-music-found p {
        font-size: 18px;
        color: #666;
    }

    /* Music Detail Styles */
    .music-detail-card {
        background-color: white;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
    }

    .music-detail-header {
        display: flex;
        flex-direction: column;
        padding: 25px;
    }

    @media (min-width: 768px) {
        .music-detail-header {
            flex-direction: row;
        }
    }

    .music-cover {
        position: relative;
        width: 100%;
        max-width: 300px;
        margin: 0 auto 20px;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
    }

    @media (min-width: 768px) {
        .music-cover {
            margin: 0 25px 0 0;
        }
    }

    .music-cover img {
        width: 100%;
        height: auto;
        display: block;
    }

    .music-cover-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .music-cover:hover .music-cover-overlay {
        opacity: 1;
    }

    .play-button {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        transform: translateY(10px);
        transition: all 0.3s ease;
    }

    .music-cover:hover .play-button {
        transform: translateY(0);
    }

    .music-info {
        flex: 1;
        padding: 10px 0;
    }

    .music-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 10px;
        line-height: 1.2;
    }

    .music-artist {
        font-size: 18px;
        color: var(--primary-color);
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }

    .music-artist i {
        margin-right: 8px;
    }

    .music-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 10px;
    }

    .music-category,
    .music-views,
    .music-date {
        font-size: 14px;
        color: #666;
        display: flex;
        align-items: center;
    }

    .music-meta i {
        margin-right: 5px;
        color: var(--primary-color);
        opacity: 0.8;
    }

    .music-player {
        padding: 20px 25px;
        border-top: 1px solid #f0f0f0;
        border-bottom: 1px solid #f0f0f0;
    }

    .music-player audio {
        width: 100%;
        height: 40px;
    }

    .music-actions {
        display: flex;
        padding: 20px 25px;
        gap: 15px;
        flex-wrap: wrap;
    }

    .action-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 12px 15px;
        border-radius: 8px;
        background-color: #f5f5f7;
        color: #333;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        min-width: 120px;
    }

    .action-btn i {
        margin-right: 8px;
        font-size: 18px;
    }

    .action-btn:hover {
        background-color: #eaeaec;
        transform: translateY(-2px);
    }

    .favorite-btn.active {
        background-color: #ffecef;
        color: #ff3a5e;
    }

    .favorite-btn:hover {
        background-color: #ffecef;
        color: #ff3a5e;
    }

    .download-btn {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
    }

    .download-btn:hover {
        background: linear-gradient(135deg, var(--primary-color) 10%, var(--secondary-color) 90%);
        box-shadow: 0 5px 15px rgba(106, 17, 203, 0.3);
        color: white;
    }

    .share-btn:hover {
        background-color: #e6f7ff;
        color: #2575fc;
    }

    /* Audio Player Styling */
    audio::-webkit-media-controls-panel {
        background-color: #f5f5f7;
    }

    audio::-webkit-media-controls-play-button {
        background-color: var(--primary-color);
        border-radius: 50%;
    }

    audio::-webkit-media-controls-time-remaining-display,
    audio::-webkit-media-controls-current-time-display {
        color: #333;
    }

    /* Comment Section Styles */
    .comment-section {
        background-color: white;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        margin-top: 40px;
        overflow: hidden;
    }

    .comment-section-header {
        padding: 20px 25px;
        border-bottom: 1px solid #f0f0f0;
    }

    .comment-section-header h2 {
        font-size: 22px;
        font-weight: 600;
        color: var(--dark-color);
        display: flex;
        align-items: center;
    }

    .comment-section-header h2 i {
        margin-right: 10px;
        color: var(--primary-color);
    }

    .comment-form-container {
        padding: 25px;
        border-bottom: 1px solid #f0f0f0;
    }

    .comment-form {
        width: 100%;
    }

    .form-row {
        margin-bottom: 20px;
    }

    .form-row label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #444;
    }

    .form-input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #e1e1e1;
        border-radius: 8px;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .form-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 10px rgba(106, 17, 203, 0.1);
        outline: none;
    }

    .comment-textarea {
        min-height: 120px;
        resize: vertical;
    }

    .form-submit-btn {
        padding: 12px 25px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
    }

    .form-submit-btn i {
        margin-right: 8px;
    }

    .form-submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(106, 17, 203, 0.2);
    }

    .login-required {
        text-align: center;
        padding: 25px;
        background-color: #f9f9f9;
        border-radius: 8px;
    }

    .login-required p {
        font-size: 16px;
        color: #666;
        margin-bottom: 15px;
    }

    .login-link {
        display: inline-block;
        padding: 10px 20px;
        background: var(--gradient);
        color: white;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .login-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(106, 17, 203, 0.2);
    }

    .comment-list {
        padding: 25px;
    }

    .comment-item {
        margin-bottom: 25px;
        padding-bottom: 25px;
        border-bottom: 1px solid #f0f0f0;
    }

    .comment-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .comment-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .comment-user {
        font-weight: 600;
        color: var(--dark-color);
        display: flex;
        align-items: center;
    }

    .comment-user i {
        margin-right: 8px;
        color: var(--primary-color);
    }

    .comment-date {
        font-size: 13px;
        color: #888;
    }

    .comment-content {
        font-size: 15px;
        color: #555;
        line-height: 1.6;
    }

    .no-comments {
        text-align: center;
        padding: 20px 0;
        color: #666;
    }

    .no-comments i {
        font-size: 32px;
        color: #ddd;
        margin-bottom: 10px;
    }

    /* Lyrics Section Styles - Đơn giản hóa */
    .lyrics-container {
        margin-top: 0;
        background-color: white;
        border-top: 1px solid #f0f0f0;
        padding: 20px 25px;
    }

    .lyrics-header {
        margin-bottom: 15px;
    }

    .lyrics-header h3 {
        font-size: 20px;
        font-weight: 600;
        color: var(--dark-color);
    }

    .lyrics-header h3 i {
        margin-right: 8px;
        color: var(--primary-color);
    }

    .lyrics-content {
        max-height: 300px;
        overflow-y: auto;
        padding-right: 10px;
        line-height: 1.8;
        color: #555;
        font-size: 16px;
    }

    .no-lyrics {
        text-align: center;
        padding: 30px 0;
        color: #888;
    }

    .no-lyrics i {
        font-size: 36px;
        color: #ddd;
        margin-bottom: 10px;
    }
</style>

<div class="music-page-container">
    <div class="page-title">
        <h1>Now Playing</h1>
    </div>

    <?php if (!empty($row)): ?>
        <div class="music-player-section">
            <div class="music-detail-card">
                <div class="music-detail-header">
                    <div class="music-cover">
                        <img src="<?= ROOT ?>/<?= $row['image'] ?>" alt="<?= esc($row['title']) ?>">
                        <div class="music-cover-overlay">
                            <div class="play-button">
                                <i class="fas fa-play"></i>
                            </div>
                        </div>
                    </div>

                    <div class="music-info">
                        <h1 class="music-title"><?= esc($row['title']) ?></h1>
                        <div class="music-artist">
                            <i class="fas fa-microphone"></i> <?= esc(get_artist($row['artist_id'])) ?>
                        </div>
                        <div class="music-meta">
                            <div class="music-category">
                                <i class="fas fa-tag"></i> <?= esc(get_category($row['category_id'])) ?>
                            </div>
                            <div class="music-views">
                                <i class="fas fa-eye"></i> <?= number_format($row['views']) ?> views
                            </div>
                            <div class="music-date">
                                <i class="fas fa-calendar-alt"></i> <?= get_date($row['date']) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="music-player">
                    <audio controls>
                        <source src="<?= ROOT ?>/<?= $row['file'] ?>" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>

                <div class="music-actions">
                    <?php if (logged_in()): ?>
                        <button type="button" class="action-btn favorite-btn <?= $isFavorited ? 'active' : '' ?>" data-song-id="<?= $row['id'] ?>">
                            <i class="<?= $isFavorited ? 'fas' : 'far' ?> fa-heart"></i>
                            <span><?= $isFavorited ? 'Added to Favorites' : 'Add to Favorites' ?></span>
                        </button>
                    <?php else: ?>
                        <a href="<?= ROOT ?>/login" class="action-btn">
                            <i class="far fa-heart"></i>
                            <span>Login to Favorite</span>
                        </a>
                    <?php endif; ?>

                    <a href="<?= ROOT ?>/download/<?= $row['slug'] ?>" class="action-btn download-btn">
                        <i class="fas fa-download"></i>
                        <span>Download</span>
                    </a>

                    <button type="button" class="action-btn share-btn">
                        <i class="fas fa-share-alt"></i>
                        <span>Share</span>
                    </button>
                </div>

                <div class="lyrics-container">
                    <div class="lyrics-header">
                        <h3><i class="fas fa-music"></i> Lyrics</h3>
                    </div>
                    <div class="lyrics-content" id="lyrics-content" style="white-space: pre-line; padding: 10px;">
                        <?php if (!empty($row['lyrics'])): ?>
                            <?= htmlspecialchars($row['lyrics']) ?>
                        <?php else: ?>
                            <div class="no-lyrics">
                                <i class="fas fa-file-alt"></i>
                                <p>Lyrics not available for this song.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="comment-section">
            <div class="comment-section-header">
                <h2><i class="fas fa-comments"></i> Comments</h2>
            </div>

            <?php if (logged_in()): ?>
                <div class="comment-form-container">
                    <form class="comment-form" method="post" action="">
                        <div class="form-row">
                            <label for="user_name">Your Name</label>
                            <input type="text" id="user_name" name="user_name" class="form-input" placeholder="Enter your name" required value="<?= user('username') ?>">
                        </div>

                        <div class="form-row">
                            <label for="comment">Your Comment</label>
                            <textarea id="comment" name="comment" class="form-input comment-textarea" placeholder="Share your thoughts on this song..." required></textarea>
                        </div>

                        <button type="submit" class="form-submit-btn">
                            <i class="fas fa-paper-plane"></i> Post Comment
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="login-required">
                    <p>You need to be logged in to post comments.</p>
                    <a href="<?= ROOT ?>/login" class="login-link">
                        <i class="fas fa-sign-in-alt"></i> Login to Comment
                    </a>
                </div>
            <?php endif; ?>

            <div class="comment-list">
                <?php
                $query = "SELECT * FROM comments WHERE song_id = :song_id ORDER BY created_at DESC";
                $comments = db_query($query, ['song_id' => $row['id']]);

                if (!empty($comments)):
                    foreach ($comments as $comment): ?>
                        <div class="comment-item">
                            <div class="comment-header">
                                <div class="comment-user">
                                    <i class="fas fa-user-circle"></i>
                                    <?= htmlspecialchars($comment['user_name']) ?>
                                </div>
                                <div class="comment-date">
                                    <?= date("F j, Y, g:i a", strtotime($comment['created_at'])) ?>
                                </div>
                            </div>
                            <div class="comment-content">
                                <?= htmlspecialchars($comment['comment']) ?>
                            </div>
                        </div>
                    <?php endforeach;
                else: ?>
                    <div class="no-comments">
                        <i class="far fa-comment-dots"></i>
                        <p>No comments yet. Be the first to share your thoughts!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="no-music-found">
            <i class="fas fa-music"></i>
            <p>Sorry, the song you are looking for does not exist.</p>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle favorite functionality
        document.querySelectorAll('.favorite-btn').forEach(favoriteBtn => {
            favoriteBtn.addEventListener('click', function() {
                const songId = this.getAttribute('data-song-id');
                const btn = this;
                const icon = btn.querySelector('i');
                const text = btn.querySelector('span');

                // Toggle button appearance
                btn.classList.toggle('active');

                if (btn.classList.contains('active')) {
                    icon.className = 'fas fa-heart';
                    text.textContent = 'Added to Favorites';
                } else {
                    icon.className = 'far fa-heart';
                    text.textContent = 'Add to Favorites';
                }

                // Send AJAX request
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
                            // Rollback UI changes if error
                            btn.classList.toggle('active');
                            if (btn.classList.contains('active')) {
                                icon.className = 'fas fa-heart';
                                text.textContent = 'Added to Favorites';
                            } else {
                                icon.className = 'far fa-heart';
                                text.textContent = 'Add to Favorites';
                            }
                            console.error('Error:', data.message);
                        }
                    })
                    .catch(error => {
                        // Rollback UI changes if network error
                        btn.classList.toggle('active');
                        if (btn.classList.contains('active')) {
                            icon.className = 'fas fa-heart';
                            text.textContent = 'Added to Favorites';
                        } else {
                            icon.className = 'far fa-heart';
                            text.textContent = 'Add to Favorites';
                        }
                        console.error('Network error:', error);
                    });
            });
        });

        // Handle play button click
        const musicCover = document.querySelector('.music-cover');
        const audioPlayer = document.querySelector('audio');

        if (musicCover && audioPlayer) {
            musicCover.querySelector('.play-button').addEventListener('click', function() {
                if (audioPlayer.paused) {
                    audioPlayer.play();
                } else {
                    audioPlayer.pause();
                }
            });
        }

        // Share functionality
        document.querySelector('.share-btn')?.addEventListener('click', function() {
            if (navigator.share) {
                navigator.share({
                        title: document.querySelector('.music-title').textContent,
                        text: `Check out this song: ${document.querySelector('.music-title').textContent} by ${document.querySelector('.music-artist').textContent.trim()}`,
                        url: window.location.href
                    })
                    .catch(error => console.log('Error sharing:', error));
            } else {
                // Fallback for browsers that don't support navigator.share
                // Copy URL to clipboard
                const tempInput = document.createElement('input');
                tempInput.value = window.location.href;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);

                // Show feedback
                alert('Link copied to clipboard!');
            }
        });
    });
</script>

<?php require page('includes/footer') ?>