<?php
require page('includes/header');

// Hàm để trích xuất đoạn lyrics phù hợp với từ khóa tìm kiếm
function extract_lyrics_snippet($lyrics, $search_term, $snippet_length = 200)
{
    if (empty($lyrics) || empty($search_term)) return '';

    $search_term = strtolower($search_term);
    $lyrics_lower = strtolower($lyrics);
    $position = strpos($lyrics_lower, $search_term);

    if ($position === false) return '';

    // Tính toán vị trí bắt đầu và độ dài của đoạn trích
    $start = max(0, $position - floor($snippet_length / 2));
    $length = $snippet_length;

    // Điều chỉnh để không cắt giữa từ
    if ($start > 0) {
        // Tìm khoảng trắng gần nhất ở đầu
        $space_pos = strpos($lyrics, ' ', $start);
        if ($space_pos !== false && $space_pos < $position) {
            $start = $space_pos + 1;
        }
    }

    // Cắt đoạn trích
    $snippet = substr($lyrics, $start, $length);

    // Thêm dấu ... nếu cần
    if ($start > 0) $snippet = '...' . $snippet;
    if ($start + $length < strlen($lyrics)) $snippet .= '...';

    return $snippet;
}

// Hàm để highlight từ khóa tìm kiếm
function highlight_text($text, $search_term) {
    if(empty($search_term) || empty($text)) return $text;
    
    $highlighted = "<span class='highlight'>$0</span>";
    $pattern = '/' . preg_quote($search_term, '/') . '/i';
    
    // Thử thực hiện preg_replace, nếu có lỗi thì trả về text gốc
    $result = @preg_replace($pattern, $highlighted, $text);
    return $result !== null ? $result : $text;
}
?>

<link rel="stylesheet" type="text/css" href="<?= ROOT ?>/assets/css/music.css">

<div class="music-container">
    <div class="section-header">
        <h2 class="section-title">Explore Music</h2>
        <p class="section-description">Discover your next favorite songs from our collection</p>
    </div>

    <div class="music-content">
        <div class="music-filter">
            <a href="<?= ROOT ?>/music" class="filter-item <?= empty($_GET['search']) ? 'active' : '' ?>">All Tracks</a>
            <div class="filter-item">Popular</div>
            <div class="filter-item">New Releases</div>
            <div class="filter-item">Trending</div>
            <form method="get" action="<?= ROOT ?>/music" class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Tìm kiếm bài hát, nghệ sĩ hoặc lời bài hát..." value="<?= isset($_GET['search']) ? esc($_GET['search']) : '' ?>">
            </form>
        </div>

        <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
            <div class="search-info">
                <p>Searching for "<strong><?= esc($_GET['search']) ?></strong>"</p>
            </div>
        <?php endif; ?>

        <div class="songs-grid">
            <?php
            $limit = 20;
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $page = $page < 1 ? 1 : $page;
            $offset = ($page - 1) * $limit;

            // Thay thế truy vấn Full-Text Search bằng LIKE
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search_term = trim($_GET['search']);
                
                // Kiểm tra độ dài của từ khóa tìm kiếm (MySQL yêu cầu ít nhất 3 ký tự cho FULLTEXT)
                if (strlen($search_term) < 3) {
                    // Tìm kiếm LIKE thông thường cho từ khóa ngắn
                    $query = "SELECT songs.*, artists.name as artist_name 
                              FROM songs 
                              JOIN artists ON songs.artist_id = artists.id
                              WHERE songs.title LIKE :like_search 
                                 OR artists.name LIKE :like_search
                                 OR songs.lyrics LIKE :like_search
                              ORDER BY songs.views DESC 
                              LIMIT $limit OFFSET $offset";
                              
                    $params = ['like_search' => '%' . $search_term . '%'];
                    
                    // Truy vấn đếm tổng số kết quả
                    $count_query = "SELECT COUNT(*) as total 
                                    FROM songs 
                                    JOIN artists ON songs.artist_id = artists.id
                                    WHERE songs.title LIKE :like_search 
                                       OR artists.name LIKE :like_search
                                       OR songs.lyrics LIKE :like_search";
                } else {
                    // Sử dụng FULLTEXT SEARCH cho từ khóa đủ dài
                    try {
                        $query = "SELECT songs.*, artists.name as artist_name,
                                  MATCH(songs.lyrics) AGAINST(:search IN BOOLEAN MODE) as relevance
                                  FROM songs 
                                  JOIN artists ON songs.artist_id = artists.id
                                  WHERE MATCH(songs.lyrics) AGAINST(:search IN BOOLEAN MODE)
                                     OR songs.title LIKE :like_search 
                                     OR artists.name LIKE :like_search
                                  ORDER BY relevance DESC, songs.views DESC 
                                  LIMIT $limit OFFSET $offset";
                        
                        $params = [
                            'search' => $search_term . '*', // Thêm * để tìm các từ bắt đầu bằng search_term
                            'like_search' => '%' . $search_term . '%'
                        ];
                        
                        // Truy vấn đếm tổng số kết quả
                        $count_query = "SELECT COUNT(*) as total 
                                        FROM songs 
                                        JOIN artists ON songs.artist_id = artists.id
                                        WHERE MATCH(songs.lyrics) AGAINST(:search IN BOOLEAN MODE)
                                           OR songs.title LIKE :like_search 
                                           OR artists.name LIKE :like_search";
                                           
                        // Thử thực hiện truy vấn
                        $test = db_query($query, $params);
                        
                        // Nếu có lỗi, quay lại LIKE
                        if ($test === false) {
                            throw new Exception("FULLTEXT search failed");
                        }
                    } catch (Exception $e) {
                        // Fallback to LIKE search if FULLTEXT fails
                        $query = "SELECT songs.*, artists.name as artist_name 
                                  FROM songs 
                                  JOIN artists ON songs.artist_id = artists.id
                                  WHERE songs.title LIKE :like_search 
                                     OR artists.name LIKE :like_search
                                     OR songs.lyrics LIKE :like_search
                                  ORDER BY songs.views DESC 
                                  LIMIT $limit OFFSET $offset";
                        
                        $params = ['like_search' => '%' . $search_term . '%'];
                        
                        // Truy vấn đếm tổng số kết quả
                        $count_query = "SELECT COUNT(*) as total 
                                        FROM songs 
                                        JOIN artists ON songs.artist_id = artists.id
                                        WHERE songs.title LIKE :like_search 
                                           OR artists.name LIKE :like_search
                                           OR songs.lyrics LIKE :like_search";
                    }
                }
                
                $rows = db_query($query, $params);
                $total_count = db_query_one($count_query, $params);
                $total = $total_count['total'] ?? 0;
            } else {
                // Không có tìm kiếm, hiển thị tất cả bài hát
                $rows = db_query("SELECT songs.*, artists.name as artist_name
                                  FROM songs 
                                  JOIN artists ON songs.artist_id = artists.id
                                  ORDER BY songs.views DESC 
                                  LIMIT $limit OFFSET $offset");
            
                // Lấy tổng số bài hát cho phân trang
                $total_count = db_query_one("SELECT COUNT(*) as total FROM songs");
                $total = $total_count['total'] ?? 0;
            }

            // Tính toán thông tin phân trang
            $total_pages = ceil($total / $limit);
            $prev_page = $page - 1;
            $next_page = $page + 1;

            // Đảm bảo các giá trị phân trang hợp lệ
            $prev_page = $prev_page < 1 ? 1 : $prev_page;
            $next_page = $next_page > $total_pages ? $total_pages : $next_page;
            ?>

            <?php if (!empty($rows)): ?>
                <?php foreach ($rows as $row): ?>
                    <?php
                    // Đoạn này ở phần sau foreach($rows as $row)
                    if (isset($_GET['search']) && !empty($_GET['search'])) {
                        // Tạo các biến mới để lưu trữ phiên bản đã highlight
                        $row['title_highlighted'] = highlight_text(esc($row['title']), $_GET['search']);
                        $row['artist_name_highlighted'] = highlight_text(esc($row['artist_name']), $_GET['search']);
                    } else {
                        $row['title_highlighted'] = esc($row['title']);
                        $row['artist_name_highlighted'] = esc($row['artist_name']);
                    }

                    include page('includes/music-card');
                    ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-songs">
                    <i class="fas fa-music"></i>
                    <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                        <p>No songs found matching "<?= esc($_GET['search']) ?>"</p>
                    <?php else: ?>
                        <p>No songs found</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="pagination">
            <?php
            // Xây dựng URL phân trang với tham số tìm kiếm (nếu có)
            $url_params = '';
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $url_params = '&search=' . urlencode($_GET['search']);
            }
            ?>
            <a href="<?= ROOT ?>/music?page=<?= $prev_page . $url_params ?>" class="pagination-btn prev <?= $page <= 1 ? 'disabled' : '' ?>">
                <i class="fas fa-chevron-left"></i> Previous Page
            </a>
            <div class="page-indicator">
                Page <?= $page ?> of <?= $total_pages > 0 ? $total_pages : 1 ?>
                <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                    <span class="result-count">(<?= $total ?> results)</span>
                <?php endif; ?>
            </div>
            <a href="<?= ROOT ?>/music?page=<?= $next_page . $url_params ?>" class="pagination-btn next <?= $page >= $total_pages ? 'disabled' : '' ?>">
                Next Page <i class="fas fa-chevron-right"></i>
            </a>
        </div>
    </div>
</div>

<?php require page('includes/footer') ?>