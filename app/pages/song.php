<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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

    // Xử lý khi người dùng gửi bình luận (phải đảm bảo rằng $row có giá trị)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (logged_in()) { // Kiểm tra xem người dùng có đăng nhập không
            $user_name = trim($_POST['user_name']);
            $comment = trim($_POST['comment']);
            $song_id = $row['id']; // Lấy ID bài hát từ bảng songs

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

<?php require page('includes/header')?>

<style>
    /* Căn chỉnh chung */
    .section-title {
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
    }
    .song-container {
        display: flex;
        flex-direction: column; /* Đặt bài nhạc và các phần khác theo chiều dọc */
        justify-content: center;
        align-items: center;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 0 50px 0 50px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        max-width: 900px; /* Giới hạn chiều rộng */
        margin: 20px auto; /* Căn giữa */
    }
    .song-info {
        max-width: 800px;
        text-align: left;
        padding: 20px;
    }
    .song-title {
        font-size: 32px;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .song-details {
        margin-bottom: 20px;
        color: #555;
    }
    .no-song {
        text-align: center;
        color: red;
        font-size: 18px;
        margin-top: 50px;
    }

    /* Trang trí lại form bình luận */
    .comment-form {
        margin-top: 40px;
        background-color: #f1f1f1;
        padding: 20px;
        border-radius: 8px;
        width: 100%; /* Cho form full width */
        max-width: 800px; /* Giới hạn chiều rộng */
        margin: 40px auto;
    }

    .comment-form input, .comment-form textarea {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
    }

    .comment-form input::placeholder, .comment-form textarea::placeholder {
        color: #888;
    }

    .comment-form button {
        padding: 12px 24px;
        background-color: #28a745;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .comment-form button:hover {
        background-color: #218838;
    }

    /* Bình luận */
    .comment-list {
        margin-top: 40px;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }

    .comment-list .comment {
        margin-bottom: 20px;
        padding: 15px;
        background-color: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    }

    .comment-list .comment .comment-author {
        font-weight: bold;
        color: #333;
        margin-bottom: 8px;
    }

    .comment-list .comment .comment-time {
        font-size: 12px;
        color: #888;
        margin-bottom: 10px;
    }

    .comment-list .comment .comment-text {
        font-size: 16px;
        color: #555;
    }

    .error-message {
        color: red;
        text-align: center;
        margin-top: 20px;
    }

</style>

<center><div class="section-title">Now Playing</div></center>

<section class="content">
    
    <?php if (!empty($row)): ?>
     
        <div class="song-container">
            <?php include page('song-full') ?>
        </div>

        <?php if (logged_in()): ?>
        
            <div class="comment-form">
                <form method="post" action="">
                <div class="nav-item"><a href="<?=ROOT?>/admin/users/edit/<?=user('id')?>">Profile</a></div>
                    <input type="text" name="user_name" placeholder="Your Name" required>
                    <textarea name="comment" rows="5" placeholder="Your Comment" required></textarea>
                    <button type="submit">Submit Comment</button>
                </form>
                
            </div>
        <?php else: ?>
            <div class="error-message">You must be logged in to comment. <a href="http://localhost/music_website/public/login">Login here</a></div>
        <?php endif; ?>

        
        <div class="comment-list">
            <h3>Comments</h3>

            <?php 
       
            $query = "SELECT * FROM comments WHERE song_id = :song_id ORDER BY created_at DESC";
            $comments = db_query($query, ['song_id' => $row['id']]);

            if (!empty($comments)): 
                foreach ($comments as $comment): ?>
                    <div class="comment">
                        <div class="comment-author"><?= htmlspecialchars($comment['user_name']) ?></div>
                        <div class="comment-time"><?= date("F j, Y, g:i a", strtotime($comment['created_at'])) ?></div>
                        <div class="comment-text"><?= htmlspecialchars($comment['comment']) ?></div>
                    </div>
                <?php endforeach;
            else: ?>
                <p>No comments yet. Be the first to comment!</p>
            <?php endif; ?>
        </div>

    <?php else: ?>
       
        <div class="no-song">
            <p>Sorry, the song you are looking for does not exist.</p>
        </div>
    <?php endif; ?>

</section>

<?php require page('includes/footer')?>
