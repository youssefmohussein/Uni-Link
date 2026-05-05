<?php
// Clear all sessions and login attempts
session_start();

// Clear login attempts from session
unset($_SESSION['login_attempts']);

// Destroy entire session
session_destroy();

// Start fresh session
session_start();

echo "Sessions cleared! Login attempts reset.\n";
echo "You can now try logging in again.\n";
?>
