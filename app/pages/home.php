<?php require page('includes/header')?>
<link rel="stylesheet" type="text/css" href="<?=ROOT?>/assets/css/home.css">
<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-container">
        <img class="hero-image" src="<?=ROOT?>/assets/images/background.jpg" alt="SoundWave">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Welcome to <span>SOUND<span class="highlight">WAVE</span></span></h1>
            <p>Discover and enjoy your favorite music</p>
            <a href="<?=ROOT?>/music" class="hero-button">Explore Music</a>
        </div>
    </div>
</section>

<!-- Featured Section -->
<section class="featured-section">
    <div class="section-header">
        <h2 class="section-title">Featured</h2>
        <a href="<?=ROOT?>/music" class="section-link">View All <i class="fas fa-chevron-right"></i></a>
    </div>

    <div class="songs-container">
        <?php 
            // $rows = db_query("select * from songs where featured = 1 order by id desc limit 16");
            $rows = db_query("select * from songs order by id desc limit 16");
        ?>

        <?php if(!empty($rows)):?>
            <div class="songs-grid">
                <?php foreach($rows as $row):?>
                    <?php include page('includes/music-card')?>
                <?php endforeach;?>
            </div>
        <?php else:?>
            <div class="no-songs-message">
                <i class="fas fa-music"></i>
                <p>No songs found</p>
            </div>
        <?php endif;?>
    </div>
</section>

<?php require page('includes/footer')?>