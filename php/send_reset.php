<?php
require __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Reset Link</title>
    <link rel="stylesheet" href="../css/login_style.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .reset-btn-form {
            margin-bottom: 30px;
        }
        .register-link {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>Password Reset</h1>
        <div class="input-box" style="display:block;">
            <?php
            if (!empty($_POST['email'])) {
                $email = $_POST['email'];
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user) {
                    $token = bin2hex(random_bytes(32));
                    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

                    $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
                    $stmt->execute([$token, $expires, $email]);

                    $reset_link = "http://localhost/sites/site.loc/www/php/reset_password.php?token=$token";
                    echo "<p>Your password reset link:</p>";
                    echo "<form action='$reset_link' method='get' class='reset-btn-form'>";
                    echo "<button type='submit' class='btn'>Reset Password</button>";
                    echo "</form>";
                } else {
                    echo "If this email is registered, the reset link will be shown below.";
                }
            } else {
                echo "Please enter your email.";
            }
            ?>
        </div>
        <div class="register-link">
            <p><a href="login.php">Back to login</a></p>
        </div>
    </div>
</body>
</html>