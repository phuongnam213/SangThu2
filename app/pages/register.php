<?php 

if($_SERVER['REQUEST_METHOD'] == "POST")
{
    $errors = [];
    $values = [];

    // Lấy dữ liệu từ form
    $values['username'] = trim($_POST['username']);
    $values['email'] = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Kiểm tra nếu email đã tồn tại
    $query = "select * from users where email = :email limit 1";
    $row = db_query_one($query, ['email' => $values['email']]);
    
    if(!empty($row)) {
        $errors[] = "Email đã tồn tại.";
    }

    // Nếu không có lỗi, tiến hành thêm tài khoản
    if(empty($errors)) {
        $values['password'] = password_hash($password, PASSWORD_BCRYPT); // mã hóa mật khẩu
        $values['date'] = date("Y-m-d H:i:s"); // thời gian đăng ký
        $values['role'] = 'user'; // đặt vai trò mặc định là 'user'

        // Thêm tài khoản vào cơ sở dữ liệu
        $query = "insert into users (username, email, password, date, role) values (:username, :email, :password, :date, :role)";
        db_query($query, $values);

        message("Đăng ký thành công! Hãy đăng nhập.");
        redirect('login'); // chuyển hướng về trang đăng nhập
    } else {
        message(implode("<br>", $errors));
    }
}

?>

<?php require page('includes/header')?>

<!-- Add this line to include the login-specific CSS -->
<link rel="stylesheet" type="text/css" href="<?=ROOT?>/assets/css/login.css">

<section class="content">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="<?=ROOT?>/assets/images/logo.jpg" class="login-logo" alt="Logo">
            </div>
            
            <div class="login-form">
                <h2>Create Account</h2>
                
                <?php if(message()):?>
                    <div class="alert">
                        <i class="fas fa-exclamation-circle"></i> <?=message('', true)?>
                    </div>
                <?php endif;?>

                <form method="post">
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" placeholder="Username" required 
                               class="form-input" value="<?=set_value('username')?>">
                    </div>
                    
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email Address" required 
                               class="form-input" value="<?=set_value('email')?>">
                    </div>
                    
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required 
                               class="form-input" value="<?=set_value('password')?>">
                    </div>
                    
                    <div class="terms-privacy">
                        <label class="custom-checkbox">
                            <input type="checkbox" name="terms" required> 
                            <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></span>
                        </label>
                    </div>
                    
                    <button type="submit" class="login-btn">Create Account</button>
                </form>
                
                <div class="register-link">
                    <p>Already have an account? <a href="<?=ROOT?>/login">Sign In</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require page('includes/footer')?>