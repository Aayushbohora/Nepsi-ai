<?php
session_start();
if (!($_SESSION['admin_logged_in'] ?? false)) {
    header('Location: index.php');
    exit;
}

// Load data files
$statsFile = __DIR__ . '/../data/stats.json';
$usersFile = __DIR__ . '/../data/users.json';
$codesFile = __DIR__ . '/../data/codes.json';
$promptsFile = __DIR__ . '/../data/prompts.txt';

$stats = json_decode(file_get_contents($statsFile), true) ?? [];
$users = json_decode(file_get_contents($usersFile), true) ?? [];
$codes = json_decode(file_get_contents($codesFile), true) ?? [];
$prompts = file_exists($promptsFile) ? file_get_contents($promptsFile) : '';

function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    if ($diff < 60) return $diff . " seconds ago";
    if ($diff < 3600) return floor($diff/60) . " minutes ago";
    if ($diff < 86400) return floor($diff/3600) . " hours ago";
    return floor($diff/86400) . " days ago";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Dashboard</title>
<style>
body { font-family: Arial,sans-serif; background: #f9f9f9; margin: 0; padding: 20px; }
.container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 10px; }
h1 { text-align: center; }
section { margin-bottom: 30px; }
table { width: 100%; border-collapse: collapse; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background: #222; color: white; }
a.button { display: inline-block; padding: 8px 15px; background: #222; color: white; text-decoration: none; border-radius: 6px; margin-bottom: 10px;}
a.button:hover { background: #555; }
.logout { float: right; }
</style>
</head>
<body>
<div class="container">
  <a href="index.php?logout=1" class="button logout">Logout</a>
  <h1>Admin Dashboard</h1>

  <section>
    <h2>Stats</h2>
    <p><strong>Total Generated:</strong> <?= $stats['totalGenerated'] ?? 0 ?></p>
    <p><strong>Successful Loads:</strong> <?= $stats['successfulCount'] ?? 0 ?></p>
    <p><strong>Failed Loads:</strong> <?= $stats['failedCount'] ?? 0 ?></p>
    <p><strong>VIP Users:</strong> <?= count(array_filter($users, fn($u) => $u['vip'] ?? false)) ?></p>
    <p><strong>Total Users:</strong> <?= count($users) ?></p>
  </section>

  <section>
    <h2>VIP Codes</h2>
    <a href="create_vip.php" class="button">Create New VIP Code</a>
    <table>
      <tr><th>Code</th><th>Status</th><th>Used By</th><th>Used At</th></tr>
      <?php foreach ($codes as $c): ?>
      <tr>
        <td><?= htmlspecialchars($c['code']) ?></td>
        <td><?= $c['used'] ? 'Used' : 'Unused' ?></td>
        <td><?= htmlspecialchars($c['used_by'] ?? '') ?></td>
        <td><?= isset($c['used_at']) ? timeAgo($c['used_at']) : '' ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </section>

  <section>
    <h2>Prompt History (Last 500 lines)</h2>
    <pre style="max-height:300px;overflow:auto;background:#eee;padding:10px;border-radius:6px;"><?= htmlspecialchars(implode("\n", array_slice(explode("\n", $prompts), -500))) ?></pre>
  </section>
</div>
</body>
</html>
