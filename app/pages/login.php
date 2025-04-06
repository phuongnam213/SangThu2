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
            message("Login successful");
            redirect('admin');  				
        }
    }

    message("Wrong email or password"); 
}

?>

<?php require page('includes/header')?>

<section class="content">
 
    <div class="login-holder">

        <?php if(message()):?>
            <div class="alert"><?=message('', true)?></div>
        <?php endif;?>

        <form method="post">
            <center><img src="assets/images/logo.jpg" style="width: 150px; border-radius: 50%; border: solid thin #ccc;"></center>
            <h2>Login</h2>
            <input value="<?=set_value('email')?>" class="my-1 form-control" type="email" name="email" placeholder="Email" required>
            <input value="<?=set_value('password')?>" class="my-1 form-control" type="password" name="password" placeholder="Password" required>
            <button class="my-1 btn bg-blue">Login</button>
        </form>
        
        <!-- Thêm nút đăng ký ở đây -->
        <div class="register-link" style="text-align: center; margin-top: 15px;">
            <p>Don't have an account? <a href="http://localhost/music_website/public/register">Sign up here</a></p>
        </div>
    </div>
</section>

<?php require page('includes/footer')?>
