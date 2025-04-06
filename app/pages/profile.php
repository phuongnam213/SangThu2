<?php
// Đảm bảo user đã đăng nhập
if(!logged_in()) {
    message("Bạn cần đăng nhập để xem trang này");
    redirect('login');
}

// Lấy thông tin user hiện tại
$user_id = user('id');
$query = "select * from users where id = :id limit 1";
$row = db_query_one($query, ['id' => $user_id]);

// Xử lý form submit
if($_SERVER['REQUEST_METHOD'] == "POST")
{
    $errors = [];

    // Kiểm tra mật khẩu (nếu có)
    if(!empty($_POST['password']))
    {
        if($_POST['password'] != $_POST['retype_password']){
            $errors['password'] = "Mật khẩu không khớp";
        }else
        if(strlen($_POST['password']) < 8)
        {
            $errors['password'] = "Mật khẩu phải có ít nhất 8 ký tự";
        }
    }

    // Nếu không có lỗi, cập nhật thông tin
    if(empty($errors))
    {
        $values = [];
        $values['id'] = $user_id;

        $query = ""; // Không cập nhật gì nếu không có mật khẩu mới

        // Nếu có mật khẩu mới, chỉ cập nhật mật khẩu
        if(!empty($_POST['password']))
        {
            $query = "update users set password = :password where id = :id limit 1";
            $values['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            
            db_query($query, $values);

            // Cập nhật thông tin session
            $new_data = db_query_one("select * from users where id = :id limit 1", ['id' => $user_id]);
            if($new_data) {
                authenticate($new_data);
            }

            message("Mật khẩu đã được cập nhật thành công");
            redirect('profile');
        }
    }
}
?>

<?php require page('includes/header')?>

<style>
    /* Profile Page Styles */
    .profile-container {
        max-width: 900px;
        margin: 40px auto;
        padding: 0 20px;
    }
    
    .profile-header {
        text-align: center;
        margin-bottom: 40px;
    }
    
    .profile-title {
        font-size: 32px;
        font-weight: 700;
        color: var(--dark-color);
        position: relative;
        display: inline-block;
        margin-bottom: 10px;
    }
    
    .profile-title:after {
        content: '';
        position: absolute;
        width: 60px;
        height: 3px;
        background: var(--gradient);
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
    }
    
    .profile-subtitle {
        color: #555;
        margin-top: 20px;
    }
    
    .profile-content {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
    }
    
    .profile-sidebar {
        flex: 1;
        min-width: 250px;
    }
    
    .profile-main {
        flex: 2;
        min-width: 300px;
    }
    
    .profile-card {
        background-color: white;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        padding: 0;
        margin-bottom: 30px;
    }
    
    .profile-card-header {
        background: var(--gradient);
        color: white;
        padding: 20px 25px;
        position: relative;
    }
    
    .profile-card-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 600;
        display: flex;
        align-items: center;
    }
    
    .profile-card-header h3 i {
        margin-right: 10px;
    }
    
    .profile-card-body {
        padding: 25px;
    }
    
    .user-info-item {
        margin-bottom: 25px;
        display: flex;
        align-items: center;
    }
    
    .user-info-item:last-child {
        margin-bottom: 0;
    }
    
    .user-info-icon {
        width: 40px;
        height: 40px;
        background: var(--light-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        color: var(--primary-color);
    }
    
    .user-info-text {
        flex: 1;
    }
    
    .user-info-label {
        font-size: 12px;
        color: #666;
        margin-bottom: 5px;
    }
    
    .user-info-value {
        font-size: 16px;
        color: #333;
        font-weight: 500;
    }
    
    .profile-form .input-group {
        margin-bottom: 25px;
    }
    
    .profile-form label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
        color: #444;
    }
    
    .profile-form .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #e1e1e1;
        border-radius: 8px;
        font-size: 15px;
        transition: all 0.3s ease;
    }
    
    .profile-form .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 10px rgba(106, 17, 203, 0.1);
        outline: none;
    }
    
    .profile-form .form-control-readonly {
        background-color: #f9f9f9;
        color: #666;
        cursor: not-allowed;
    }
    
    .profile-submit-btn {
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
    
    .profile-submit-btn i {
        margin-right: 8px;
    }
    
    .profile-submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(106, 17, 203, 0.2);
    }
    
    .user-nav {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .user-nav-item {
        padding: 15px 25px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .user-nav-item:last-child {
        border-bottom: none;
    }
    
    .user-nav-link {
        display: flex;
        align-items: center;
        color: #444;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .user-nav-link i {
        margin-right: 12px;
        color: var(--primary-color);
        width: 20px;
        text-align: center;
    }
    
    .user-nav-link:hover {
        color: var(--primary-color);
        transform: translateX(5px);
    }
    
    .user-nav-item.active {
        background-color: rgba(106, 17, 203, 0.05);
    }
    
    .user-nav-item.active .user-nav-link {
        color: var(--primary-color);
        font-weight: 500;
    }
    
    .user-avatar {
        text-align: center;
        padding: 30px 0;
    }
    
    .avatar-img {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .avatar-name {
        margin-top: 15px;
        font-size: 18px;
        font-weight: 600;
        color: #333;
    }
    
    .avatar-role {
        font-size: 14px;
        color: #666;
        margin-top: 5px;
    }
    
    .error {
        color: #ff3a5e;
        font-size: 12px;
        margin-top: 5px;
        margin-bottom: 5px;
        display: block;
    }
    
    .success-alert {
        background-color: #fff;
        border-left: 4px solid #4caf50;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        color: #4caf50;
        animation: fadeIn 0.5s ease;
    }
    
    .info-badge {
        display: inline-block;
        padding: 3px 8px;
        background-color: rgba(106, 17, 203, 0.1);
        color: var(--primary-color);
        border-radius: 4px;
        font-size: 12px;
        margin-left: 10px;
        vertical-align: middle;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    @media (max-width: 768px) {
        .profile-content {
            flex-direction: column;
        }
        
        .profile-sidebar, .profile-main {
            width: 100%;
        }
    }
</style>

<div class="profile-container">
    <div class="profile-header">
        <h1 class="profile-title">Trang cá nhân</h1>
        <p class="profile-subtitle">Xem và cập nhật thông tin cá nhân của bạn</p>
    </div>
    
    <div class="profile-content">
        <div class="profile-sidebar">
            <div class="profile-card">
                <div class="user-avatar">
                    <img src="<?=ROOT?>/assets/images/default-avatar.jpg" alt="User Avatar" class="avatar-img">
                    <div class="avatar-name"><?=esc(user('username'))?></div>
                    <div class="avatar-role"><?=ucfirst(user('role'))?></div>
                </div>
                
                <ul class="user-nav">
                    <li class="user-nav-item active">
                        <a href="<?=ROOT?>/profile" class="user-nav-link">
                            <i class="fas fa-user"></i> Thông tin cá nhân
                        </a>
                    </li>
                    <li class="user-nav-item">
                        <a href="<?=ROOT?>/favorites" class="user-nav-link">
                            <i class="fas fa-heart"></i> Bài hát yêu thích
                        </a>
                    </li>
                    <li class="user-nav-item">
                        <a href="<?=ROOT?>/playlists" class="user-nav-link">
                            <i class="fas fa-list"></i> Danh sách phát
                        </a>
                    </li>
                    <li class="user-nav-item">
                        <a href="<?=ROOT?>/logout" class="user-nav-link">
                            <i class="fas fa-sign-out-alt"></i> Đăng xuất
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="profile-main">
            <div class="profile-card">
                <div class="profile-card-header">
                    <h3><i class="fas fa-lock"></i> Đổi mật khẩu</h3>
                </div>
                
                <div class="profile-card-body">
                    <?php if(message()): ?>
                        <div class="success-alert">
                            <i class="fas fa-check-circle"></i> <?=message('', true)?>
                        </div>
                    <?php endif; ?>

                    <form method="post" class="profile-form">
                        <div class="input-group">
                            <label for="username">Tên người dùng <span class="info-badge">Chỉ xem</span></label>
                            <input type="text" id="username" class="form-control form-control-readonly" value="<?=esc($row['username'])?>" readonly>
                        </div>

                        <div class="input-group">
                            <label for="email">Email <span class="info-badge">Chỉ xem</span></label>
                            <input type="email" id="email" class="form-control form-control-readonly" value="<?=esc($row['email'])?>" readonly>
                        </div>

                        <div class="input-group">
                            <label for="password">Mật khẩu mới</label>
                            <input type="password" id="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới nếu muốn thay đổi">
                            <?php if(!empty($errors['password'])): ?>
                                <small class="error"><i class="fas fa-exclamation-circle"></i> <?=$errors['password']?></small>
                            <?php endif; ?>
                        </div>

                        <div class="input-group">
                            <label for="retype_password">Nhập lại mật khẩu mới</label>
                            <input type="password" id="retype_password" name="retype_password" class="form-control" placeholder="Nhập lại mật khẩu mới">
                        </div>

                        <button type="submit" class="profile-submit-btn">
                            <i class="fas fa-save"></i> Cập nhật mật khẩu
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="profile-card">
                <div class="profile-card-header">
                    <h3><i class="fas fa-info-circle"></i> Thông tin tài khoản</h3>
                </div>
                
                <div class="profile-card-body">
                    <div class="user-info-item">
                        <div class="user-info-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-info-text">
                            <div class="user-info-label">Tên người dùng</div>
                            <div class="user-info-value"><?=esc($row['username'])?></div>
                        </div>
                    </div>
                    
                    <div class="user-info-item">
                        <div class="user-info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="user-info-text">
                            <div class="user-info-label">Email</div>
                            <div class="user-info-value"><?=esc($row['email'])?></div>
                        </div>
                    </div>
                    
                    <div class="user-info-item">
                        <div class="user-info-icon">
                            <i class="fas fa-user-tag"></i>
                        </div>
                        <div class="user-info-text">
                            <div class="user-info-label">Vai trò</div>
                            <div class="user-info-value"><?=ucfirst($row['role'])?></div>
                        </div>
                    </div>
                    
                    <div class="user-info-item">
                        <div class="user-info-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="user-info-text">
                            <div class="user-info-label">Ngày tham gia</div>
                            <div class="user-info-value"><?=get_date($row['date'])?></div>
                        </div>
                    </div>
                    
                    <p style="margin-top: 20px; color: #666; font-size: 14px;">
                        <i class="fas fa-info-circle"></i> Nếu bạn muốn thay đổi tên người dùng hoặc email, vui lòng liên hệ với quản trị viên.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require page('includes/footer')?>