<?php
session_start();

$adminUser = 'admin';
$adminPass = 'yourStrongPassword'; // Change this to a strong password!

if (isset($_POST['username'], $_POST['password'])) {
    if ($_POST['username'] === $adminUser && $_POST['password'] === $adminPass) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Login</title>
  <style>
    body { font-family: Arial,sans-serif; background: #eee; padding: 40px; }
    .login-box { background: white; padding: 30px; max-width: 400px; margin: auto; border-radius: 12px; box-shadow: 0 0 12px #aaa; }
    input { display: block; margin: 15px 0; padding: 10px; width: 100%; border-radius: 6px; border: 1px solid #ccc; }
    button { padding: 12px; width: 100%; background: #222; color: white; border: none; border-radius: 6px; cursor: pointer; }
    button:hover { background: #555; }
    .error { color: red; font-weight: bold; }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Admin Login</h2>
    <?php if (!empty($error)): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" action="">
      <input type="text" name="username" placeholder="Username" required autofocus />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
