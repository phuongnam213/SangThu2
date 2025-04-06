<div class="artist-card">
    <a href="<?=ROOT?>/artist-detail/<?=$row['id']?>" class="artist-card-link">
        <div class="artist-card-image">
            <img src="<?=ROOT?>/<?=$row['image']?>" alt="<?=esc($row['name'])?>">
            <div class="artist-overlay">
                <div class="artist-icon">
                    <i class="fas fa-microphone-alt"></i>
                </div>
            </div>
        </div>
        <div class="artist-card-content">
            <h3 class="artist-card-name"><?=esc($row['name'])?></h3>
            <?php
                // Lấy thông tin bổ sung về nghệ sĩ (nếu có)
                $bio = $row['bio'] ?? '';
                $bio_short = (strlen($bio) > 60) ? substr($bio, 0, 60) . '...' : $bio;
            ?>
            <div class="artist-card-bio"><?=$bio_short?></div>
        </div>
    </a>
</div>

<style>
    /* Artist Card Styles */
    .artist-card {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .artist-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .artist-card-link {
        display: block;
        text-decoration: none;
        color: inherit;
    }
    
    .artist-card-image {
        position: relative;
        width: 100%;
        padding-top: 100%; /* 1:1 Aspect Ratio */
        overflow: hidden;
    }
    
    .artist-card-image img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .artist-card:hover .artist-card-image img {
        transform: scale(1.05);
    }
    
    .artist-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.3);
        opacity: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: opacity 0.3s ease;
    }
    
    .artist-card:hover .artist-overlay {
        opacity: 1;
    }
    
    .artist-icon {
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
    
    .artist-card:hover .artist-icon {
        transform: scale(1);
    }
    
    .artist-card-content {
        padding: 15px;
    }
    
    .artist-card-name {
        font-size: 16px;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 5px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .artist-card-bio {
        font-size: 13px;
        color: #666;
        line-height: 1.4;
        max-height: 36px;
        overflow: hidden;
    }
</style>