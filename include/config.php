<?php
// Update these values for your local MySQL setup.
$dbServer = 'localhost';
$dbUser = 'root';
$dbPass = 'password';
$dbName = 'myhmsdb';

if (!defined('DB_SERVER')) {
define('DB_SERVER', $dbServer);
}
if (!defined('DB_USER')) {
define('DB_USER', $dbUser);
}
if (!defined('DB_PASS')) {
define('DB_PASS', $dbPass);
}
if (!defined('DB_NAME')) {
define('DB_NAME', $dbName);
}
if (!isset($con)) {
$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
}
if (!$con) {
die('Database connection failed. Check DB credentials in include/config.php. MySQL error: ' . mysqli_connect_error());
}

// SMTP settings for forgot-password OTP emails.
// Replace with your real SMTP credentials.
if (!defined('HMS_SMTP_HOST')) {
define('HMS_SMTP_HOST', 'smtp.gmail.com');
}
if (!defined('HMS_SMTP_PORT')) {
define('HMS_SMTP_PORT', 587);
}
if (!defined('HMS_SMTP_USERNAME')) {
define('HMS_SMTP_USERNAME', 'your-email@example.com');
}
if (!defined('HMS_SMTP_PASSWORD')) {
define('HMS_SMTP_PASSWORD', 'your-app-password');
}
if (!defined('HMS_SMTP_SECURE')) {
define('HMS_SMTP_SECURE', 'tls');
}
if (!defined('HMS_MAIL_FROM')) {
define('HMS_MAIL_FROM', 'your-email@example.com');
}
if (!defined('HMS_MAIL_FROM_NAME')) {
define('HMS_MAIL_FROM_NAME', 'Global Hospital');
}
?>