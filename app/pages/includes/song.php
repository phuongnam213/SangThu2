<!--start music card-->
<link rel="stylesheet" type="text/css" href="<?= ROOT ?>/assets/css/music-card.css">

<div class="music-card">
    <div class="music-card-image">
        <a href="<?= ROOT ?>/music-playing/<?= $row['slug'] ?>">
            <img src="<?= ROOT ?>/<?= $row['image'] ?>" alt="<?= esc($row['title']) ?>">
            <div class="play-overlay">
                <span class="play-icon">
                    <i class="fas fa-play"></i>
                </span>
            </div>
        </a>
    </div>
    <div class="music-card-content">
        <h3 class="music-card-title"><?= $row['title_highlighted'] ?? esc($row['title']) ?></h3>
        <div class="music-card-artist"><?= $row['artist_name_highlighted'] ?? esc(get_artist($row['artist_id'])) ?></div>
        <div class="music-card-category">
            <span class="category-badge"><?= esc(get_category($row['category_id'])) ?></span>
        </div>
    </div>
    <?php if (isset($_GET['search']) && !empty($_GET['search']) && !empty($row['lyrics'])): ?>
        <?php
        // Tìm kiếm theo từ khóa trong lời bài hát
        $lyrics_snippet = extract_lyrics_snippet($row['lyrics'], $_GET['search']);

        // Nếu không tìm thấy từ khóa trong lời bài hát (tìm theo tên hoặc nghệ sĩ)
        // thì vẫn hiển thị một đoạn trích lời bài hát
        if (empty($lyrics_snippet) && (
            strpos(strtolower($row['title']), strtolower($_GET['search'])) !== false ||
            strpos(strtolower($row['artist_name']), strtolower($_GET['search'])) !== false
        )) {
            // Lấy 200 ký tự đầu tiên của lời bài hát
            $lyrics_snippet = substr($row['lyrics'], 0, 200);
            if (strlen($row['lyrics']) > 200) {
                $lyrics_snippet .= '...';
            }
        }

        if (!empty($lyrics_snippet)):
        ?>
            <div class="lyrics-preview">
                <?php echo highlight_text($lyrics_snippet, $_GET['search']); ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<!--end music card-->