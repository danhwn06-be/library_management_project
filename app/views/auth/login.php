<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - TVAN Library</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="brand-header">
        <img src="images/logo.png" alt="Logo">
        <span>TVAN LIBRARY</span>
    </div>

    <div class="login-container">
        <div class="left-form">
            <p class="brand-sub">TVAN LIBRARY</p>
            <h1>Welcome back !</h1>

            <?php if (isset($message) && $message): ?>
            <div class="msg"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="Type your email" required>
                </div>

                <div class="form-group" style="position: relative;">
                    <label>Password</label>
                    <input type="password" name="password" id="pass" placeholder="Type your password" required>

                </div>

                <button type="submit" name="login" class="btn-in">Sign In</button>
                <a href="index.php?action=register" class="btn-reg">Register</a>

            </form>


        </div>

        <div class="right-img"
            style="background-image: url('https://images.unsplash.com/photo-1507842217343-583bb7270b66');">
        </div>
    </div>
</body>

</html>
