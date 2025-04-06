<!-- Sửa đổi đoạn mã xử lý đăng nhập trong login.php -->
<?php 
if($_SERVER['REQUEST_METHOD'] == "POST")
{
    $errors = [];
    $values = [];
    $values['email'] = trim($_POST['email']);
    $query = "select * from users where email = :email limit 1";
    $row = db_query_one($query, $values);
    
    if(!empty($row))
    {
        if(password_verify($_POST['password'], $row['password']))
        {
            authenticate($row);
            
            // Đăng nhập thành công, chuyển hướng người dùng về trang chủ
            redirect('home');
        }
    }

    message("Wrong email or password"); 
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
                <h2>Welcome Back</h2>
                
                <?php if(message()):?>
                    <div class="alert">
                        <i class="fas fa-exclamation-circle"></i> <?=message('', true)?>
                    </div>
                <?php endif;?>

                <form method="post">
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
                    
                    <div class="remember-forgot">
                        <label class="custom-checkbox">
                            <input type="checkbox" name="remember"> Remember me
                        </label>
                        <a href="#" class="forgot-link">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" class="login-btn">Sign In</button>
                </form>
                
                <div class="register-link">
                    <p>Don't have an account? <a href="<?=ROOT?>/register">Create One</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require page('includes/footer')?>