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
?>