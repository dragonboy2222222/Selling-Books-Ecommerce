<?php
$plain = "MyStrongPassword123";   // choose your new admin password
$hash  = password_hash($plain, PASSWORD_DEFAULT);

echo "Password: $plain\n";
echo "Hash: $hash\n";
