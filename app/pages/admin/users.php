<?php 
// Điều chỉnh phần validation trong phần 'add'
if($action == 'add')
{
    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
        $errors = [];

        // Validation cho username
        if(empty($_POST['username']))
        {
            $errors['username'] = "A username is required";
        }else
        if(!preg_match("/^[a-zA-Z0-9]+$/", $_POST['username'])){
            $errors['username'] = "A username can only have letters and numbers with no spaces";
        }

        // Các validation khác giữ nguyên
        if(empty($_POST['email']))
        {
            $errors['email'] = "An email is required";
        }else
        if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)){
            $errors['email'] = "Email not valid";
        }

        if(empty($_POST['password']))
        {
            $errors['password'] = "A password is required";
        }else
        if($_POST['password'] != $_POST['retype_password']){
            $errors['password'] = "Passwords do not match";
        }else
        if(strlen($_POST['password']) < 8)
        {
            $errors['password'] = "Password must be 8 characters or more";
        }

        if(empty($_POST['role']))
        {
            $errors['role'] = "A role is required";
        }

        if(empty($errors))
        {
            $values = [];
            $values['username'] = trim($_POST['username']);
            $values['email'] = trim($_POST['email']);
            $values['role'] = trim($_POST['role']);
            $values['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $values['date'] = date("Y-m-d H:i:s");

            $query = "insert into users (username,email,password,role,date) values (:username,:email,:password,:role,:date)";
            db_query($query,$values);

            message("User created successfully");
            redirect('admin/users');
        }
    }
}else
if($action == 'edit')
{
    $query = "select * from users where id = :id limit 1";
    $row = db_query_one($query,['id'=>$id]);

    if($_SERVER['REQUEST_METHOD'] == "POST" && $row)
    {
        $errors = [];

        // Validation cho username trong phần edit
        if(empty($_POST['username']))
        {
            $errors['username'] = "A username is required";
        }else
        if(!preg_match("/^[a-zA-Z0-9]+$/", $_POST['username'])){
            $errors['username'] = "A username can only have letters and numbers with no spaces";
        }

        // Các validation khác giữ nguyên
        if(empty($_POST['email']))
        {
            $errors['email'] = "An email is required";
        }else
        if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)){
            $errors['email'] = "Email not valid";
        }

        if(!empty($_POST['password']))
        {
            if($_POST['password'] != $_POST['retype_password']){
                $errors['password'] = "Passwords do not match";
            }else
            if(strlen($_POST['password']) < 8)
            {
                $errors['password'] = "Password must be 8 characters or more";
            }
        }

        if(empty($_POST['role']))
        {
            $errors['role'] = "A role is required";
        }

        if(empty($errors))
        {
            $values = [];
            $values['username'] = trim($_POST['username']);
            $values['email'] = trim($_POST['email']);
            $values['role'] = trim($_POST['role']);
            $values['id'] = $id;

            $query = "update users set email = :email, username = :username, role = :role where id = :id limit 1";

            if(!empty($_POST['password']))
            {
                $query = "update users set email = :email, password = :password, username = :username, role = :role where id = :id limit 1";
                $values['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }

            db_query($query,$values);

            message("User edited successfully");
            redirect('admin/users');
        }
    }
}else
if($action == 'delete')
{
    $query = "select * from users where id = :id limit 1";
    $row = db_query_one($query,['id'=>$id]);

    if($_SERVER['REQUEST_METHOD'] == "POST" && $row)
    {
        $errors = [];

        if($row['id'] == 1)
        {
            $errors['username'] = "The main admin cannot be deleted";
        }

        if(empty($errors))
        {
            $values = [];
            $values['id'] = $id;

            $query = "delete from users where id = :id limit 1";
            db_query($query,$values);

            message("User deleted successfully");
            redirect('admin/users');
        }
    }
}
?>

<?php require page('includes/admin-header')?>

<!-- Custom CSS for Admin -->
<style>
    /* Admin Panel Styles - Using SoundWave Theme */
    .admin-content {
        background-color: white;
        border-radius: 16px;
        box-shadow: var(--box-shadow);
        padding: 30px;
        margin: 20px auto;
        max-width: 1200px;
    }
    
    .admin-card {
        background-color: white;
        border-radius: 16px;
        box-shadow: var(--box-shadow);
        padding: 25px;
        margin: 0 auto;
    }
    
    .admin-card h3 {
        color: var(--dark-color);
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--secondary-color);
        font-weight: 600;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #e1e1e1;
        border-radius: 8px;
        font-size: 15px;
        transition: all 0.3s ease;
        margin-bottom: 15px;
    }
    
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 10px rgba(106, 17, 203, 0.1);
        outline: none;
    }
    
    .my-1 {
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .btn {
        padding: 10px 20px;
        border-radius: 30px;
        border: none;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
        font-size: 14px;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    
    .bg-purple {
        background: var(--gradient);
        color: white;
    }
    
    .bg-orange {
        background: linear-gradient(135deg, #ff9a44 0%, #ff3a5e 100%);
        color: white;
    }
    
    .bg-red {
        background: linear-gradient(135deg, #ff5252 0%, #ff1744 100%);
        color: white;
    }
    
    .float-end {
        float: right;
    }
    
    .error {
        color: #ff3a5e;
        font-size: 12px;
        margin-top: -10px;
        margin-bottom: 10px;
        display: block;
    }
    
    .alert {
        background-color: #fff;
        border-left: 4px solid #ff5252;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        color: #ff5252;
    }
    
    .success-alert {
        background-color: #fff;
        border-left: 4px solid #4caf50;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        color: #4caf50;
    }
    
    /* Table Styles */
    .table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        background-color: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: var(--box-shadow);
    }
    
    .table th, .table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .table th {
        background: var(--gradient);
        color: white;
        font-weight: 500;
    }
    
    .table tr:hover {
        background-color: rgba(106, 17, 203, 0.03);
    }
    
    .table tr:last-child td {
        border-bottom: none;
    }
    
    .action-icons {
        display: flex;
        gap: 10px;
    }
    
    .action-icon {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s ease;
    }
    
    .edit-icon {
        background-color: rgba(37, 117, 252, 0.1);
        color: var(--secondary-color);
    }
    
    .delete-icon {
        background-color: rgba(255, 58, 94, 0.1);
        color: var(--accent-color);
    }
    
    .action-icon:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 10px rgba(0,0,0,0.1);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .admin-content {
            padding: 15px;
            margin: 10px;
        }
        
        .table th, .table td {
            padding: 8px 10px;
            font-size: 14px;
        }
        
        .btn {
            padding: 8px 15px;
            font-size: 13px;
        }
    }
</style>

<section class="admin-content">
    <?php if($action == 'add'): ?>
        <div class="admin-card" style="max-width: 500px;">
            <form method="post">
                <h3><i class="fas fa-user-plus"></i> Add New User</h3>

                <?php if(message()): ?>
                    <div class="success-alert">
                        <i class="fas fa-check-circle"></i> <?=message('', true)?>
                    </div>
                <?php endif; ?>

                <div class="input-group">
                    <input class="form-control" value="<?=set_value('username')?>" type="text" name="username" placeholder="Username">
                    <?php if(!empty($errors['username'])): ?>
                        <small class="error"><i class="fas fa-exclamation-circle"></i> <?=$errors['username']?></small>
                    <?php endif; ?>
                </div>

                <div class="input-group">
                    <input class="form-control" value="<?=set_value('email')?>" type="email" name="email" placeholder="Email">
                    <?php if(!empty($errors['email'])): ?>
                        <small class="error"><i class="fas fa-exclamation-circle"></i> <?=$errors['email']?></small>
                    <?php endif; ?>
                </div>

                <div class="input-group">
                    <select name="role" class="form-control">
                        <option value="">--Select Role--</option>
                        <option <?=set_select('role','user')?> value="user">User</option>
                        <option <?=set_select('role','admin')?> value="admin">Admin</option>
                    </select>
                    <?php if(!empty($errors['role'])): ?>
                        <small class="error"><i class="fas fa-exclamation-circle"></i> <?=$errors['role']?></small>
                    <?php endif; ?>
                </div>

                <div class="input-group">
                    <input class="form-control" value="<?=set_value('password')?>" type="password" name="password" placeholder="Password">
                    <?php if(!empty($errors['password'])): ?>
                        <small class="error"><i class="fas fa-exclamation-circle"></i> <?=$errors['password']?></small>
                    <?php endif; ?>
                </div>

                <div class="input-group">
                    <input class="form-control" value="<?=set_value('retype_password')?>" type="password" name="retype_password" placeholder="Retype Password">
                </div>
                
                <div class="action-buttons">
                    <button class="btn bg-orange"><i class="fas fa-save"></i> Save</button>
                    <a href="<?=ROOT?>/admin/users">
                        <button type="button" class="float-end btn"><i class="fas fa-arrow-left"></i> Back</button>
                    </a>
                </div>
            </form>
        </div>

    <?php elseif($action == 'edit'): ?>
        <div class="admin-card" style="max-width: 500px;">
            <form method="post">
                <h3><i class="fas fa-user-edit"></i> Edit User</h3>

                <?php if(message()): ?>
                    <div class="success-alert">
                        <i class="fas fa-check-circle"></i> <?=message('', true)?>
                    </div>
                <?php endif; ?>

                <?php if(!empty($row)): ?>
                    <div class="input-group">
                        <input class="form-control" value="<?=set_value('username',$row['username'])?>" type="text" name="username" placeholder="Username">
                        <?php if(!empty($errors['username'])): ?>
                            <small class="error"><i class="fas fa-exclamation-circle"></i> <?=$errors['username']?></small>
                        <?php endif; ?>
                    </div>

                    <div class="input-group">
                        <input class="form-control" value="<?=set_value('email',$row['email'])?>" type="email" name="email" placeholder="Email">
                        <?php if(!empty($errors['email'])): ?>
                            <small class="error"><i class="fas fa-exclamation-circle"></i> <?=$errors['email']?></small>
                        <?php endif; ?>
                    </div>

                    <div class="input-group">
                        <select name="role" class="form-control">
                            <option value="">--Select Role--</option>
                            <option <?=set_select('role','user',$row['role'])?> value="user">User</option>
                            <option <?=set_select('role','admin',$row['role'])?> value="admin">Admin</option>
                        </select>
                        <?php if(!empty($errors['role'])): ?>
                            <small class="error"><i class="fas fa-exclamation-circle"></i> <?=$errors['role']?></small>
                        <?php endif; ?>
                    </div>

                    <div class="input-group">
                        <input class="form-control" value="<?=set_value('password')?>" type="password" name="password" placeholder="Password (leave empty to keep old one)">
                        <?php if(!empty($errors['password'])): ?>
                            <small class="error"><i class="fas fa-exclamation-circle"></i> <?=$errors['password']?></small>
                        <?php endif; ?>
                    </div>

                    <div class="input-group">
                        <input class="form-control" value="<?=set_value('retype_password')?>" type="password" name="retype_password" placeholder="Retype Password">
                    </div>
                    
                    <div class="action-buttons">
                        <button class="btn bg-orange"><i class="fas fa-save"></i> Save</button>
                        <a href="<?=ROOT?>/admin/users">
                            <button type="button" class="float-end btn"><i class="fas fa-arrow-left"></i> Back</button>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="alert">
                        <i class="fas fa-exclamation-triangle"></i> That record was not found
                    </div>
                    <a href="<?=ROOT?>/admin/users">
                        <button type="button" class="btn"><i class="fas fa-arrow-left"></i> Back</button>
                    </a>
                <?php endif; ?>
            </form>
        </div>

    <?php elseif($action == 'delete'): ?>
        <div class="admin-card" style="max-width: 500px;">
            <form method="post">
                <h3><i class="fas fa-user-minus"></i> Delete User</h3>

                <?php if(!empty($row)): ?>
                    <div class="user-info">
                        <div class="form-control"><?=set_value('username',$row['username'])?></div>
                        <?php if(!empty($errors['username'])): ?>
                            <small class="error"><i class="fas fa-exclamation-circle"></i> <?=$errors['username']?></small>
                        <?php endif; ?>

                        <div class="form-control"><?=set_value('email',$row['email'])?></div>
                        <div class="form-control"><?=set_value('role',$row['role'])?></div>
                    </div>
                    
                    <div class="confirmation-message" style="margin: 20px 0; padding: 15px; background-color: rgba(255,82,82,0.1); border-radius: 8px;">
                        <p><i class="fas fa-exclamation-triangle"></i> Are you sure you want to delete this user? This action cannot be undone.</p>
                    </div>
                    
                    <div class="action-buttons">
                        <button class="btn bg-red"><i class="fas fa-trash"></i> Delete</button>
                        <a href="<?=ROOT?>/admin/users">
                            <button type="button" class="float-end btn"><i class="fas fa-arrow-left"></i> Back</button>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="alert">
                        <i class="fas fa-exclamation-triangle"></i> That record was not found
                    </div>
                    <a href="<?=ROOT?>/admin/users">
                        <button type="button" class="btn"><i class="fas fa-arrow-left"></i> Back</button>
                    </a>
                <?php endif; ?>
            </form>
        </div>

    <?php else: ?>
        <?php 
            $query = "select * from users order by id desc limit 20";
            $rows = db_query($query);
        ?>
        <div class="admin-card">
            <div class="title-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3><i class="fas fa-users"></i> Users Management</h3>
                <a href="<?=ROOT?>/admin/users/add">
                    <button class="btn bg-purple"><i class="fas fa-plus"></i> Add New</button>
                </a>
            </div>

            <?php if(message()): ?>
                <div class="success-alert">
                    <i class="fas fa-check-circle"></i> <?=message('', true)?>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($rows)): ?>
                            <?php foreach($rows as $row): ?>
                                <tr>
                                    <td><?=$row['id']?></td>
                                    <td><?=$row['username']?></td>
                                    <td><?=$row['email']?></td>
                                    <td>
                                        <span class="role-badge" style="padding: 3px 10px; border-radius: 20px; font-size: 12px; 
                                            <?= $row['role'] == 'admin' ? 
                                                'background: var(--gradient); color: white;' : 
                                                'background: rgba(37, 117, 252, 0.1); color: var(--secondary-color);' ?>">
                                            <?=$row['role']?>
                                        </span>
                                    </td>
                                    <td><?=get_date($row['date'])?></td>
                                    <td>
                                        <div class="action-icons">
                                            <a href="<?=ROOT?>/admin/users/edit/<?=$row['id']?>" class="action-icon edit-icon">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?=ROOT?>/admin/users/delete/<?=$row['id']?>" class="action-icon delete-icon">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No users found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php require page('includes/admin-footer')?>