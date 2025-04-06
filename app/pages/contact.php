<?php
require page('includes/header');

// Sử dụng thư viện PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require 'C:/xampp/htdocs/music_website/PHPMailer/src/Exception.php';
require 'C:/xampp/htdocs/music_website/PHPMailer/src/PHPMailer.php';
require 'C:/xampp/htdocs/music_website/PHPMailer/src/SMTP.php';
// Nếu form được submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Tạo một đối tượng PHPMailer
   
    // Đường dẫn chính xác đến PHPMailer
   
    
    // Mã khác của bạn ở đây...
    

    $mail = new PHPMailer(true);

    try {
        // Cấu hình máy chủ SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'tranphuongnam212003@gmail.com'; // Thay bằng email của bạn
        $mail->Password = 'Namphuongtran0'; // Thay bằng mật khẩu ứng dụng Gmail của bạn
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Cấu hình thông tin người gửi và người nhận
        $mail->setFrom('tranphuongnam212003@gmail.com', 'Nam');
        $mail->addAddress('mymail@gmail.com'); // Thay bằng email của bạn

        // Cấu hình nội dung email
        $mail->isHTML(true);
        $mail->Subject = 'Yêu cầu liên hệ mới từ website';
        $mail->Body = "<h4>Tên: $name</h4><p>Email: $email</p><p>Tin nhắn: $message</p>";

        // Gửi email
        $mail->send();
        echo '<div class="alert alert-success">Tin nhắn của bạn đã được gửi thành công!</div>';
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Không thể gửi tin nhắn. Vui lòng thử lại sau.</div>';
    }
}
?>

<style>
    /* CSS cho trang liên hệ */
    .contact-info {
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .contact-info h4 { color: #333; font-weight: bold; margin-top: 10px; }
    .contact-info p { margin: 5px 0 15px; color: #555; }
    .contact-info a { color: #007bff; text-decoration: none; }
    .contact-info a:hover { text-decoration: underline; }
    .form-group label { font-weight: bold; color: #333; }
    .form-group input, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; margin-bottom: 15px; font-size: 14px; }
    .form-group input:focus, .form-group textarea:focus { border-color: #007bff; outline: none; box-shadow: 0 0 5px rgba(0, 123, 255, 0.25); }
    .btn-primary { background-color: #007bff; color: #fff; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; }
    .btn-primary:hover { background-color: #0056b3; }
    .social-media ul { list-style: none; padding: 0; }
    .social-media ul li { display: inline; margin-right: 15px; }
    .social-media ul li a { color: #007bff; font-size: 16px; text-decoration: none; }
    .social-media ul li a:hover { color: #0056b3; text-decoration: underline; }
</style>

<div class="container m-2">
    <h2>Contact Us</h2>
    <p>If you have any questions or need further assistance, feel free to reach out to us through the following channels:</p>

    <div class="contact-info">
        <h4>Phone Number</h4>
        <p>+1 (123) 456-7890</p>

        <h4>Email</h4>
        <p><a href="mailto:support@websitemusic.com">support@yourwebsite.com</a></p>

        <h4>Address</h4>
        <p>123 Music Lane, Suite 101<br> New York, NY 10001</p>

        <h4>Business Hours</h4>
        <p>Monday - Friday: 9:00 AM to 5:00 PM<br> Saturday: 10:00 AM to 2:00 PM<br> Sunday: Closed</p>
    </div>

    <h4>Social Media</h4>
    <div class="social-media">
        <ul>
            <li><a href="https://facebook.com/yourpage" target="_blank">Facebook</a></li>
            <li><a href="https://twitter.com/yourpage" target="_blank">Twitter</a></li>
            <li><a href="https://instagram.com/yourpage" target="_blank">Instagram</a></li>
        </ul>
    </div>

    <h4>Contact Form</h4>
    <form method="post" action="">
        <div class="form-group">
            <label for="name">Your Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <div class="form-group">
            <label for="email">Your Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="message">Your Message</label>
            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
</div>

<?php require page('includes/footer') ?>
