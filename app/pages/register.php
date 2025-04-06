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
        message("Đã xảy ra lỗi khi đăng ký.");
    }
}

?>

<?php require page('includes/header')?>

<section class="content">
 
    <div class="login-holder">

    <?php if(message()):?>
        <div class="alert"><?=message('',true)?></div>
    <?php endif;?>

        <form method="post">
            <center><img src="assets/images/logo.jpg" style="width: 150px; border-radius: 50%; border: solid thin #ccc;"></center>
            <h2>Register</h2>
            <input value="<?=set_value('username')?>" class="my-1 form-control" type="text" name="username" placeholder="Username" required>
            <input value="<?=set_value('email')?>" class="my-1 form-control" type="email" name="email" placeholder="Email" required>
            <input value="<?=set_value('password')?>" class="my-1 form-control" type="password" name="password" placeholder="Password" required>
            <button class="my-1 btn bg-blue">Register</button>
        </form>
        
        <!-- Thêm nút đăng nhập ở đây -->
        <div class="login-link" style="text-align: center; margin-top: 15px;">
            <p>Already have an account? <a href="http://localhost/music_website/public/login">Login here</a></p>
        </div>
    </div>
</section>

<?php require page('includes/footer')?>
