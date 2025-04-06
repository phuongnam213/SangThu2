<?php require page('includes/header')?>

<?php 
    $category = $URL[1] ?? null;
    $query = "select * from songs where category_id in (select id from categories where category = :category) order by views desc limit 24";
    
    $rows = db_query($query, ['category' => $category]);
    
    // Lấy thông tin mô tả về category (nếu có)
    $cat_query = "select * from categories where category = :category limit 1";
    $category_info = db_query_one($cat_query, ['category' => $category]);
?>

<link rel="stylesheet" type="text/css" href="<?=ROOT?>/assets/css/category.css">

<div class="category-container">
    <div class="section-header">
        <h2 class="section-title"><?= ucfirst(htmlspecialchars($category)) ?></h2>
        <?php if(!empty($category_info) && isset($category_info['description'])): ?>
            <p class="section-description"><?= htmlspecialchars($category_info['description']) ?></p>
        <?php else: ?>
            <p class="section-description">Explore <?= htmlspecialchars($category) ?> songs in our collection</p>
        <?php endif; ?>
    </div>

    <div class="category-content">
        <?php if(!empty($rows)): ?>
            <div class="category-stats">
                <div class="stat-item">
                    <i class="fas fa-music"></i>
                    <div class="stat-info">
                        <div class="stat-value"><?= count($rows) ?></div>
                        <div class="stat-label">Songs</div>
                    </div>
                </div>
                <?php
                    // Calculate total views
                    $total_views = 0;
                    foreach($rows as $row) {
                        $total_views += $row['views'];
                    }
                ?>
                <div class="stat-item">
                    <i class="fas fa-eye"></i>
                    <div class="stat-info">
                        <div class="stat-value"><?= number_format($total_views) ?></div>
                        <div class="stat-label">Total Views</div>
                    </div>
                </div>
                <div class="stat-item">
                    <i class="fas fa-calendar-alt"></i>
                    <div class="stat-info">
                        <div class="stat-value">
                            <?php
                                // Get the newest song date
                                $newest_date = 0;
                                foreach($rows as $row) {
                                    $date = strtotime($row['date']);
                                    if($date > $newest_date) {
                                        $newest_date = $date;
                                    }
                                }
                                echo $newest_date ? date('M d, Y', $newest_date) : 'N/A';
                            ?>
                        </div>
                        <div class="stat-label">Latest Update</div>
                    </div>
                </div>
            </div>
            
            <div class="songs-grid">
                <?php foreach($rows as $row): ?>
                    <?php include page('includes/music-card') ?>
                <?php endforeach; ?>
            </div>
            
            <div class="category-footer">
                <a href="<?=ROOT?>/music" class="back-to-music">
                    <i class="fas fa-arrow-left"></i> Back to All Music
                </a>
                <?php if(count($rows) == 24): ?>
                    <a href="<?=ROOT?>/category/<?=$category?>?load_more=true" class="load-more-btn">
                        Load More <i class="fas fa-sync"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="no-songs-found">
                <div class="no-songs-icon">
                    <i class="fas fa-music"></i>
                </div>
                <h3>No songs found in this category</h3>
                <p>We couldn't find any songs in the <?= htmlspecialchars($category) ?> category.</p>
                <a href="<?=ROOT?>/music" class="return-to-music">
                    <i class="fas fa-arrow-left"></i> Return to Music
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>



<?php require page('includes/footer')?>