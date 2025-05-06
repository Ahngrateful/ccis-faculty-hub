
<?php
// Generate user details
$email = 'newuser@example.com';
$plainPassword = bin2hex(random_bytes(4)); // 8-character random password
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

// Just print the result
echo "Email: $email<br>";
echo "Password (unhashed): $plainPassword<br>";
echo "Password (hashed): $hashedPassword<br>";
?>
