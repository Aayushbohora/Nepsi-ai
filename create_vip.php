<?php
session_start();
if (!($_SESSION['admin_logged_in'] ?? false)) {
    header('Location: index.php');
    exit;
}

$codesFile = __DIR__ . '/../data/codes.json';
$codes = json_decode(file_get_contents($codesFile), true) ?? [];

function generateCode($length = 8) {
    return strtoupper(bin2hex(random_bytes($length/2)));
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newCode = generateCode(8);
    $codes[] = [
        'code' => $newCode,
        'used' => false,
        'used_by' => null,
        'used_at' => null,
        'created_at' => date('Y-m-d H:i:s')
    ];
    file_put_contents($codesFile, json_encode($codes, JSON_PRETTY_PRINT));
    $message = "New VIP code created: <strong>{$newCode}</strong>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Create VIP Code</title>
<style>
body { font-family: Arial,sans-serif; background: #f9f9f9; padding: 20px; }
.container { max-width: 400px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 12px #aaa; }
button { background: #222; color: white; border: none; padding: 12px 20px; border-radius: 6px; cursor: pointer; }
button:hover { background: #555; }
.message { margin: 15px 0; color: green; font-weight: bold; }
a { display: inline-block; margin-top: 20px; text-decoration: none; color: #222; }
a:hover { text-decoration: underline; }
</style>
</head>
<body>
<div class="container">
  <h1>Create VIP Code</h1>
  <?php if ($message): ?>
    <p class="message"><?= $message ?></p>
  <?php endif; ?>
  <form method="POST">
    <button type="submit">Generate New VIP Code</button>
  </form>
  <a href="dashboard.php">Back to Dashboard</a>
</div>
</body>
</html>
