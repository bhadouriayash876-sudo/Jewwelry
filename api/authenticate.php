<?php
require '../auth.php';
require '../config.php';

if (!isLoggedIn()) {
    sendJSON(['authenticated' => false, 'message' => 'Not authenticated']);
}

sendJSON([
    'authenticated' => true,
    'username' => getUsername(),
    'role' => getRole(),
    'isAdmin' => isAdmin()
]);
?>
