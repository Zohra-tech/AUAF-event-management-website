<?php

$newPassword = 'AuafClub@2026';

$newHash = password_hash($newPassword, PASSWORD_DEFAULT);

echo "New password hash:<br>";
echo $newHash;
?>
