<?php
// test_password.php
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password: $password<br>";
echo "Hash: $hash<br>";
echo "Verification: " . (password_verify($password, $hash) ? 'Success' : 'Failed');
?>