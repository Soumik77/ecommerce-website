<?php
require_once __DIR__ . '/connection.inc.php';
$msg = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    require_csrf();
    $username = input_string($_POST, 'username', 100);
    $password = $_POST['password'] ?? '';
    $admin = db_one('SELECT * FROM admin_users WHERE username = ?', [$username]);
    if ($admin && is_string($password) && strlen($password) <= 72 && !str_contains($password, "\0") && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['ADMIN_LOGIN'] = 'yes';
        $_SESSION['ADMIN_ID'] = (int) $admin['id'];
        $_SESSION['ADMIN_USERNAME'] = $admin['username'];
        redirect_to('admin/categories.php');
    }
    $msg = 'Please enter valid login details.';
}
?>

<!DOCTYPE html>
<html class="no-js" lang>
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/normalize.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/pe-icon-7-filled.css">
    <link rel="stylesheet" href="assets/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/css/cs-skin-elastic.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
</head>

<body class="bg-dark">
    <div class="sufee-login d-flex align-content-center flex-wrap">
        <div class="container">
            <div class="login-content">
                <div class="login-form mt-150">
                    <h1 class="" style="text-align:center;">Admin</h1>
                    <form method="post" autocomplete="off"><?php echo csrf_field(); ?>
                    <div class="form-group">
    <label for="name">User Name</label>
    <input id="name" type="text"
           name="username"
           class="form-control" placeholder="Enter your username" autocomplete="username" required>
</div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input id="password" type="password" name="password"

                            class="form-control" placeholder="Password"
                            autocomplete="Password" required>
                        </div>
                        <button type="submit" name = "submit" class="btn btn-success btn-flat m-b-30 m-t-30">Sign in</button>

                    </form>
                    <div class="" style="color: red; margin-top:10px;">
                         <?php echo h($msg); ?>
                    </div>


                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/vendor/jquery-2.1.4.min.js" type="text/javascript"></script>
    <script src="assets/js/popper.min.js" type="text/javascript"></script>
    <script src="assets/js/plugins.js" type="text/javascript"></script>
    <script src="assets/js/main.js" type="text/javascript"></script>
</body>

</html>
