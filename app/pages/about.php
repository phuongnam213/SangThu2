<?php require page('includes/header')?>

<link rel="stylesheet" type="text/css" href="<?=ROOT?>/assets/css/about.css">

<div class="about-container">
    <div class="about-header">
        <h1 class="about-title">About SoundWave</h1>
        <div class="about-subtitle">Your Ultimate Music Experience</div>
    </div>
    
    <div class="about-content">
        <div class="about-section">
            <div class="about-image">
                <img src="<?=ROOT?>/assets/images/about-us.jpg" alt="SoundWave Music">
            </div>
            
            <div class="about-text">
                <h2>Our Story</h2>
                <p>SoundWave was founded in 2024 with a simple mission: to connect people through the universal language of music. What started as a small project has grown into a platform where music lovers can discover, share, and enjoy their favorite tunes.</p>
                
                <p>We believe that music has the power to inspire, heal, and bring people together. Our team of passionate music enthusiasts works tirelessly to create an immersive experience that celebrates artists and their craft.</p>
                
                <p>SoundWave offers a curated collection of music across various genres, from emerging artists to established stars, ensuring there's something for everyone.</p>
            </div>
        </div>
        
        <div class="values-section">
            <h2 class="section-title">Our Values</h2>
            
            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Passion for Music</h3>
                    <p>We're driven by our love for music and committed to creating a platform that honors the art form in all its diversity.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Community First</h3>
                    <p>We prioritize building a vibrant community where music lovers can connect, share, and discover together.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Quality Experience</h3>
                    <p>We're committed to providing a seamless, high-quality experience that makes exploring music enjoyable.</p>
                </div>
                
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3>Innovation</h3>
                    <p>We constantly explore new ways to enhance how people discover and experience music in the digital age.</p>
                </div>
            </div>
        </div>
        
        <div class="team-section">
            <h2 class="section-title">Our Team</h2>
            <p class="section-description">Meet the passionate individuals behind SoundWave</p>
            
            <div class="team-grid">
                <div class="team-member">
                    <div class="member-photo">
                        <img src="<?=ROOT?>/assets/images/team/member1.jpg" alt="Team Member">
                    </div>
                    <div class="member-info">
                        <h3>John Doe</h3>
                        <div class="member-role">Founder & CEO</div>
                        <p>Music enthusiast with a vision to transform how we experience music online.</p>
                    </div>
                </div>
                
                <div class="team-member">
                    <div class="member-photo">
                        <img src="<?=ROOT?>/assets/images/team/member2.jpg" alt="Team Member">
                    </div>
                    <div class="member-info">
                        <h3>Jane Smith</h3>
                        <div class="member-role">Head of Content</div>
                        <p>Curator with an ear for discovering unique sounds and emerging artists.</p>
                    </div>
                </div>
                
                <div class="team-member">
                    <div class="member-photo">
                        <img src="<?=ROOT?>/assets/images/team/member3.jpg" alt="Team Member">
                    </div>
                    <div class="member-info">
                        <h3>Michael Johnson</h3>
                        <div class="member-role">Lead Developer</div>
                        <p>Tech wizard bringing innovative solutions to enhance your music experience.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="cta-section">
            <h2>Join Our Music Community</h2>
            <p>Discover new artists, create playlists, and connect with music lovers around the world.</p>
            <div class="cta-buttons">
                <a href="<?=ROOT?>/register" class="cta-button primary">Sign Up Now</a>
                <a href="<?=ROOT?>/contact" class="cta-button secondary">Contact Us</a>
            </div>
        </div>
    </div>
</div>

<?php require page('includes/footer')?>