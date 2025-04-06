<?php require page('includes/header')?>

<div class="artist-container">
    <div class="section-header">
        <h2 class="section-title">Featured Artists</h2>
        <p class="section-description">Discover talented musicians from around the world</p>
    </div>

    <section class="artists-grid">
        <?php 
            $rows = db_query("select * from artists order by id desc limit 24");
        ?>

        <?php if(!empty($rows)):?>
            <?php foreach($rows as $row):?>
                <?php include page('includes/artist-card')?>
            <?php endforeach;?>
        <?php else:?>
            <div class="no-artists">
                <i class="fas fa-user-music"></i>
                <p>No artists found</p>
            </div>
        <?php endif;?>
    </section>
</div>

<style>
    .artist-container {
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
    
    .artists-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 30px;
    }
    
    .no-artists {
        grid-column: 1 / -1;
        text-align: center;
        padding: 50px 0;
        color: #666;
    }
    
    .no-artists i {
        font-size: 48px;
        color: #ddd;
        margin-bottom: 15px;
    }
    
    .no-artists p {
        font-size: 18px;
    }
    
    @media (max-width: 768px) {
        .artists-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
    }
    
    @media (max-width: 480px) {
        .section-title {
            font-size: 28px;
        }
        
        .artists-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }
    }
</style>

<?php require page('includes/footer')?>