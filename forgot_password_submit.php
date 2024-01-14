<?php
require('connection.inc.php');
require('functions.inc.php');

$email = get_safe_value($con, $_POST['email']);
$res = mysqli_query($con, "select * from users where email='$email'");
$check_user = mysqli_num_rows($res);

if ($check_user > 0) {
    $row = mysqli_fetch_assoc($res);
    $user_id = $row['id'];

    // Generate a unique token for password reset
    $token = bin2hex(random_bytes(16)); // You can use a more secure method for token generation

    // Update the user table with the token
    mysqli_query($con, "UPDATE users SET reset_token = '$token' WHERE id = '$user_id'");

    $reset_link = "http://DelightFashion/reset_password.php?token=$token";

    $html = "Click the following link to reset your password: <a href='$reset_link'>$reset_link</a>";

    include('smtp/PHPMailerAutoload.php');
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587;
    $mail->SMTPSecure = "tls";
    $mail->SMTPAuth = true;
    $mail->Username = "soumikdattta229@gmail.com";
    $mail->Password = "soumik12345?";
    $mail->SetFrom("soumikdattta229@gmail.com");
    $mail->addAddress($email);
    $mail->IsHTML(true);
    $mail->Subject = "Password Reset";
    $mail->Body = $html;

    try {
        $mail->send();
        echo "Please check your email for instructions to reset your password.";
    } catch (Exception $e) {
        echo "Error sending email. Please try again later.";
        error_log("Email sending failed: " . $mail->ErrorInfo);
    }
} else {
    echo "Email id not registered with us";
    die();
}
?>
