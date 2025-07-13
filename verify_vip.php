<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['vip_code'])) {
    header("Location: index.php");
    exit;
}

$code = trim($_POST['vip_code']);
$codesFile = __DIR__ . '/data/codes.json';
$codes = json_decode(file_get_contents($codesFile), true) ?? [];

$usersFile = __DIR__ . '/data/users.json';
$users = json_decode(file_get_contents($usersFile), true) ?? [];

// Find matching unused code
$found = false;
foreach ($codes as &$c) {
    if ($c['code'] === $code && !$c['used']) {
        // Mark code used and bind to this session id (device)
        $c['used'] = true;
        $c['used_by'] = session_id();
        $c['used_at'] = date('Y-m-d H:i:s');
        file_put_contents($codesFile, json_encode($codes, JSON_PRETTY_PRINT));

        // Add user as VIP
        $users[session_id()] = [
            'vip' => true,
            'vip_code' => $code,
            'vip_since' => date('Y-m-d H:i:s')
        ];
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));

        $_SESSION['is_vip'] = true;

        $found = true;
        break;
    }
}

if ($found) {
    $_SESSION['message'] = "VIP Code activated successfully! Enjoy unlimited generations.";
} else {
    $_SESSION['message'] = "Invalid or already used VIP Code.";
}

header("Location: index.php");
exit;
<?php
// End of verify_vip.php
?>