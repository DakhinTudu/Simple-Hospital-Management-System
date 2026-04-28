<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!function_exists('hms_require_role')) {
    function hms_require_role($role, $redirect = 'index.php')
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
            header('Location: ' . $redirect);
            exit();
        }
    }
}

if (!function_exists('hms_login_user')) {
    function hms_login_user($role, array $data)
    {
        session_regenerate_id(true);
        $_SESSION['role'] = $role;
        foreach ($data as $key => $value) {
            $_SESSION[$key] = $value;
        }
    }
}

if (!function_exists('hms_clean_input')) {
    function hms_clean_input($value)
    {
        return trim((string)$value);
    }
}

if (!function_exists('hms_is_valid_email')) {
    function hms_is_valid_email($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('hms_hash_password')) {
    function hms_hash_password($plainPassword)
    {
        return password_hash($plainPassword, PASSWORD_DEFAULT);
    }
}

if (!function_exists('hms_is_password_hashed')) {
    function hms_is_password_hashed($password)
    {
        return is_string($password) && strpos($password, '$2y$') === 0;
    }
}

if (!function_exists('hms_verify_password')) {
    function hms_verify_password($plainPassword, $storedPassword)
    {
        if (hms_is_password_hashed($storedPassword)) {
            return password_verify($plainPassword, $storedPassword);
        }
        return hash_equals((string)$storedPassword, (string)$plainPassword);
    }
}
?>
