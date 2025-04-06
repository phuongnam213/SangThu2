<?php require page('includes/header')?>

<link rel="stylesheet" type="text/css" href="<?=ROOT?>/assets/css/artist-detail.css">
<?php 
    $id = $URL[1] ?? null;
    $query = "select * from artists where id = :id limit 1";
    $row = db_query_one($query,['id'=>$id]);
?>

<div class="artist-profile-container">
    <?php if(!empty($row)):?>
        <div class="profile-header">
            <div class="profile-title">
                <h1>Artist Profile</h1>
            </div>
        </div>
        
        <div class="profile-content">
            <div class="artist-profile-card">
                <div class="artist-header">
                    <div class="artist-cover">
                        <img src="<?=ROOT?>/<?=$row['image']?>" alt="<?=esc($row['name'])?>">
                        <div class="artist-cover-overlay">
                            <div class="artist-cover-icon">
                                <i class="fas fa-microphone-alt"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="artist-info">
                        <h1 class="artist-name"><?=esc($row['name'])?></h1>
                        <div class="artist-meta">
                            <?php
                                // Count total songs
                                $query = "SELECT COUNT(*) as total FROM songs WHERE artist_id = :artist_id";
                                $song_count = db_query_one($query, ['artist_id' => $row['id']]);
                                
                                // Count total views
                                $query = "SELECT SUM(views) as total_views FROM songs WHERE artist_id = :artist_id";
                                $views = db_query_one($query, ['artist_id' => $row['id']]);
                                
                                $total_songs = $song_count['total'] ?? 0;
                                $total_views = $views['total_views'] ?? 0;
                            ?>
                            <div class="meta-item">
                                <i class="fas fa-music"></i>
                                <span><?=$total_songs?> Songs</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-eye"></i>
                                <span><?=number_format($total_views)?> Views</span>
                            </div>
                        </div>
                        
                        <div class="artist-bio">
                            <?=esc($row['bio'])?>
                        </div>
                    </div>
                </div>
                
                <div class="artist-songs">
                    <div class="songs-header">
                        <h2>Top Songs</h2>
                    </div>
                    
                    <div class="songs-grid">
                        <?php 
                            $query = "SELECT * FROM songs WHERE artist_id = :artist_id ORDER BY views DESC LIMIT 20";
                            $songs = db_query($query, ['artist_id' => $row['id']]);
                        ?>

                        <?php if(!empty($songs)):?>
                            <?php foreach($songs as $row):?>
                                <?php include page('includes/music-card')?>
                            <?php endforeach;?>
                        <?php else:?>
                            <div class="no-songs">
                                <i class="fas fa-music"></i>
                                <p>No songs available for this artist</p>
                            </div>
                        <?php endif;?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="back-link">
            <a href="<?=ROOT?>/artists">
                <i class="fas fa-arrow-left"></i> Back to All Artists
            </a>
        </div>
    <?php else:?>
        <div class="artist-not-found">
            <i class="fas fa-exclamation-circle"></i>
            <h2>Artist Not Found</h2>
            <p>Sorry, the artist you are looking for does not exist.</p>
            <a href="<?=ROOT?>/artists" class="return-link">
                <i class="fas fa-arrow-left"></i> Return to Artists
            </a>
        </div>
    <?php endif;?>
</div>


<?php require page('includes/footer')?>