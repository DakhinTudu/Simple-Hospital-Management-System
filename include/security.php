<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!defined('HMS_TIMEZONE')) {
    define('HMS_TIMEZONE', 'Asia/Kolkata');
}
date_default_timezone_set(HMS_TIMEZONE);

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

if (!function_exists('hms_is_logged_in')) {
    function hms_is_logged_in()
    {
        return isset($_SESSION['role']) && $_SESSION['role'] !== '';
    }
}

if (!function_exists('hms_get_actor_id')) {
    function hms_get_actor_id()
    {
        if (!isset($_SESSION['role'])) {
            return 'guest';
        }
        if ($_SESSION['role'] === 'patient') {
            return isset($_SESSION['pid']) ? 'patient#' . (int)$_SESSION['pid'] : 'patient';
        }
        if ($_SESSION['role'] === 'doctor') {
            return isset($_SESSION['dname']) ? 'doctor#' . $_SESSION['dname'] : 'doctor';
        }
        if ($_SESSION['role'] === 'admin') {
            return isset($_SESSION['username']) ? 'admin#' . $_SESSION['username'] : 'admin';
        }
        return (string)$_SESSION['role'];
    }
}

if (!function_exists('hms_clean_input')) {
    function hms_clean_input($value)
    {
        return trim((string)$value);
    }
}

if (!function_exists('hms_esc')) {
    function hms_esc($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
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

if (!function_exists('hms_csrf_token')) {
    function hms_csrf_token()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('hms_csrf_field')) {
    function hms_csrf_field()
    {
        return '<input type="hidden" name="csrf_token" value="' . hms_esc(hms_csrf_token()) . '">';
    }
}

if (!function_exists('hms_validate_csrf')) {
    function hms_validate_csrf($token)
    {
        return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('hms_enforce_csrf_on_post')) {
    function hms_enforce_csrf_on_post()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }
        $token = isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : '';
        if (!hms_validate_csrf($token)) {
            http_response_code(403);
            die('Invalid CSRF token. Please refresh and try again.');
        }
    }
}

if (!function_exists('hms_ensure_schema')) {
    function hms_ensure_schema($con)
    {
        static $initialized = false;
        if ($initialized) {
            return;
        }
        $initialized = true;

        mysqli_query($con, "CREATE TABLE IF NOT EXISTS audit_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            actor_role VARCHAR(32) NOT NULL,
            actor_id VARCHAR(100) NOT NULL,
            action VARCHAR(100) NOT NULL,
            target_type VARCHAR(100) NOT NULL,
            target_id VARCHAR(100) NOT NULL,
            metadata TEXT NULL,
            ip_address VARCHAR(45) NULL,
            created_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        mysqli_query($con, "CREATE TABLE IF NOT EXISTS password_reset_tokens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_type VARCHAR(20) NOT NULL,
            user_identifier VARCHAR(120) NOT NULL,
            otp_code VARCHAR(10) NOT NULL,
            expires_at DATETIME NOT NULL,
            consumed_at DATETIME NULL,
            created_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        mysqli_query($con, "CREATE TABLE IF NOT EXISTS appointment_slots (
            id INT AUTO_INCREMENT PRIMARY KEY,
            doctor VARCHAR(100) NOT NULL,
            slot_date DATE NOT NULL,
            slot_time TIME NOT NULL,
            status ENUM('available','booked','cancelled') NOT NULL DEFAULT 'available',
            appointment_id INT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            UNIQUE KEY uniq_slot (doctor, slot_date, slot_time)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        mysqli_query($con, "CREATE TABLE IF NOT EXISTS eprescriptiontb (
            id INT AUTO_INCREMENT PRIMARY KEY,
            appointment_id INT NOT NULL,
            patient_id INT NOT NULL,
            doctor VARCHAR(100) NOT NULL,
            medicine_name VARCHAR(255) NOT NULL,
            dosage VARCHAR(255) NOT NULL,
            duration VARCHAR(255) NOT NULL,
            instructions TEXT NULL,
            created_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        mysqli_query($con, "CREATE TABLE IF NOT EXISTS stafftb (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(120) NOT NULL,
            role ENUM('doctor','surgeon','nurse','receptionist','lab_technician','pharmacist','other') NOT NULL DEFAULT 'other',
            department VARCHAR(100) NULL,
            email VARCHAR(120) NULL,
            phone VARCHAR(20) NULL,
            status ENUM('active','inactive') NOT NULL DEFAULT 'active',
            created_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        mysqli_query($con, "CREATE TABLE IF NOT EXISTS labtesttb (
            id INT AUTO_INCREMENT PRIMARY KEY,
            appointment_id INT NOT NULL,
            pid INT NOT NULL,
            doctor VARCHAR(100) NOT NULL,
            test_name VARCHAR(150) NOT NULL,
            instructions TEXT NULL,
            result_value VARCHAR(255) NULL,
            result_notes TEXT NULL,
            status ENUM('ordered','sample_collected','completed','cancelled') NOT NULL DEFAULT 'ordered',
            ordered_at DATETIME NOT NULL,
            reported_at DATETIME NULL,
            reported_by VARCHAR(120) NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Password hashes are longer than legacy plain-text column sizes.
        // Expand columns safely so password_hash() values can be stored.
        mysqli_query($con, "ALTER TABLE admintb MODIFY password VARCHAR(255) NOT NULL");
        mysqli_query($con, "ALTER TABLE doctb MODIFY password VARCHAR(255) NOT NULL");
        mysqli_query($con, "ALTER TABLE patreg MODIFY password VARCHAR(255) NOT NULL");
        mysqli_query($con, "ALTER TABLE patreg MODIFY cpassword VARCHAR(255) NOT NULL");
        if (!function_exists('hms_column_exists')) {
            function hms_column_exists($con, $tableName, $columnName)
            {
                $table = mysqli_real_escape_string($con, $tableName);
                $column = mysqli_real_escape_string($con, $columnName);
                $result = mysqli_query($con, "SHOW COLUMNS FROM `" . $table . "` LIKE '" . $column . "'");
                return $result && mysqli_num_rows($result) > 0;
            }
        }

        if (!hms_column_exists($con, 'admintb', 'email')) {
            mysqli_query($con, "ALTER TABLE admintb ADD COLUMN email VARCHAR(120) NULL");
        }
    }
}

if (!function_exists('hms_audit_log')) {
    function hms_audit_log($con, $action, $targetType, $targetId, array $metadata = array())
    {
        $actorRole = isset($_SESSION['role']) ? (string)$_SESSION['role'] : 'guest';
        $actorId = hms_get_actor_id();
        $metaJson = !empty($metadata) ? json_encode($metadata) : null;
        $ipAddress = isset($_SERVER['REMOTE_ADDR']) ? (string)$_SERVER['REMOTE_ADDR'] : null;
        $createdAt = date('Y-m-d H:i:s');
        $stmt = mysqli_prepare($con, "INSERT INTO audit_logs(actor_role,actor_id,action,target_type,target_id,metadata,ip_address,created_at) VALUES(?,?,?,?,?,?,?,?)");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssssss", $actorRole, $actorId, $action, $targetType, $targetId, $metaJson, $ipAddress, $createdAt);
            mysqli_stmt_execute($stmt);
        }
    }
}

if (!function_exists('hms_is_slot_free')) {
    function hms_is_slot_free($con, $doctor, $appdate, $apptime)
    {
        $stmt = mysqli_prepare($con, "SELECT 1 FROM appointmenttb WHERE doctor=? AND appdate=? AND apptime=? AND userStatus='1' AND doctorStatus='1' LIMIT 1");
        mysqli_stmt_bind_param($stmt, "sss", $doctor, $appdate, $apptime);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return !$result || mysqli_num_rows($result) === 0;
    }
}

if (!function_exists('hms_book_slot')) {
    function hms_book_slot($con, $doctor, $appdate, $apptime, $appointmentId)
    {
        $createdAt = date('Y-m-d H:i:s');
        $status = 'booked';
        $stmt = mysqli_prepare($con, "INSERT INTO appointment_slots(doctor,slot_date,slot_time,status,appointment_id,created_at,updated_at) VALUES(?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE status=VALUES(status), appointment_id=VALUES(appointment_id), updated_at=VALUES(updated_at)");
        if (!$stmt) {
            return false;
        }
        mysqli_stmt_bind_param($stmt, "ssssiss", $doctor, $appdate, $apptime, $status, $appointmentId, $createdAt, $createdAt);
        return mysqli_stmt_execute($stmt);
    }
}

if (!function_exists('hms_release_slot')) {
    function hms_release_slot($con, $doctor, $appdate, $apptime)
    {
        $updatedAt = date('Y-m-d H:i:s');
        $status = 'cancelled';
        $stmt = mysqli_prepare($con, "INSERT INTO appointment_slots(doctor,slot_date,slot_time,status,appointment_id,created_at,updated_at) VALUES(?,?,?,?,NULL,?,?) ON DUPLICATE KEY UPDATE status=VALUES(status), appointment_id=NULL, updated_at=VALUES(updated_at)");
        if (!$stmt) {
            return false;
        }
        mysqli_stmt_bind_param($stmt, "ssssss", $doctor, $appdate, $apptime, $status, $updatedAt, $updatedAt);
        return mysqli_stmt_execute($stmt);
    }
}

if (!function_exists('hms_get_daily_analytics')) {
    function hms_get_daily_analytics($con)
    {
        $stats = array(
            'appointments_today' => 0,
            'cancellations_today' => 0
        );
        $today = date('Y-m-d');
        $stmt1 = mysqli_prepare($con, "SELECT COUNT(*) AS total FROM appointmenttb WHERE appdate=?");
        mysqli_stmt_bind_param($stmt1, "s", $today);
        mysqli_stmt_execute($stmt1);
        $r1 = mysqli_stmt_get_result($stmt1);
        if ($r1) {
            $row = mysqli_fetch_assoc($r1);
            $stats['appointments_today'] = (int)$row['total'];
        }
        $stmt2 = mysqli_prepare($con, "SELECT COUNT(*) AS total FROM appointmenttb WHERE appdate=? AND (userStatus='0' OR doctorStatus='0')");
        mysqli_stmt_bind_param($stmt2, "s", $today);
        mysqli_stmt_execute($stmt2);
        $r2 = mysqli_stmt_get_result($stmt2);
        if ($r2) {
            $row = mysqli_fetch_assoc($r2);
            $stats['cancellations_today'] = (int)$row['total'];
        }
        return $stats;
    }
}

if (!function_exists('hms_get_doctor_load')) {
    function hms_get_doctor_load($con, $limit = 10)
    {
        $rows = array();
        $sql = "SELECT doctor, COUNT(*) AS total_appointments,
                SUM(CASE WHEN userStatus='0' OR doctorStatus='0' THEN 1 ELSE 0 END) AS total_cancelled
                FROM appointmenttb
                GROUP BY doctor
                ORDER BY total_appointments DESC
                LIMIT " . (int)$limit;
        $result = mysqli_query($con, $sql);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
        }
        return $rows;
    }
}

if (!function_exists('hms_get_appointments_trend')) {
    function hms_get_appointments_trend($con, $days = 7)
    {
        $days = max(1, (int)$days);
        $startDate = date('Y-m-d', strtotime('-' . ($days - 1) . ' days'));
        $labels = array();
        $bookings = array();
        $cancellations = array();
        $lookup = array();

        $stmt = mysqli_prepare($con, "SELECT appdate,
                    COUNT(*) AS total_bookings,
                    SUM(CASE WHEN userStatus='0' OR doctorStatus='0' THEN 1 ELSE 0 END) AS total_cancellations
                FROM appointmenttb
                WHERE appdate >= ?
                GROUP BY appdate
                ORDER BY appdate ASC");
        mysqli_stmt_bind_param($stmt, "s", $startDate);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $lookup[$row['appdate']] = array(
                    'bookings' => (int)$row['total_bookings'],
                    'cancellations' => (int)$row['total_cancellations']
                );
            }
        }

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime('-' . $i . ' days'));
            $labels[] = date('d M', strtotime($date));
            $bookings[] = isset($lookup[$date]) ? $lookup[$date]['bookings'] : 0;
            $cancellations[] = isset($lookup[$date]) ? $lookup[$date]['cancellations'] : 0;
        }

        return array(
            'labels' => $labels,
            'bookings' => $bookings,
            'cancellations' => $cancellations
        );
    }
}

if (!function_exists('hms_get_specialization_load')) {
    function hms_get_specialization_load($con)
    {
        $rows = array();
        $query = "SELECT d.spec,
                  COUNT(a.ID) AS total_appointments
                  FROM doctb d
                  LEFT JOIN appointmenttb a ON a.doctor = d.username
                  GROUP BY d.spec
                  ORDER BY total_appointments DESC";
        $result = mysqli_query($con, $query);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = array(
                    'spec' => $row['spec'],
                    'total_appointments' => (int)$row['total_appointments']
                );
            }
        }
        return $rows;
    }
}

if (!function_exists('hms_get_slot_utilization_today')) {
    function hms_get_slot_utilization_today($con)
    {
        $today = date('Y-m-d');
        $doctorCount = 0;
        $doctorResult = mysqli_query($con, "SELECT COUNT(*) AS total_doctors FROM doctb");
        if ($doctorResult) {
            $doctorRow = mysqli_fetch_assoc($doctorResult);
            $doctorCount = (int)$doctorRow['total_doctors'];
        }

        $defaultSlotsPerDoctor = 5;
        $capacity = $doctorCount * $defaultSlotsPerDoctor;
        $booked = 0;

        $stmt = mysqli_prepare($con, "SELECT COUNT(*) AS total_booked
                FROM appointmenttb
                WHERE appdate=? AND userStatus='1' AND doctorStatus='1'");
        mysqli_stmt_bind_param($stmt, "s", $today);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res) {
            $row = mysqli_fetch_assoc($res);
            $booked = (int)$row['total_booked'];
        }

        // Use unique doctor+time slots for true slot occupancy, not raw booking rows.
        $slotsBooked = 0;
        $slotStmt = mysqli_prepare($con, "SELECT COUNT(*) AS total_slots
                FROM (
                    SELECT doctor, apptime
                    FROM appointmenttb
                    WHERE appdate=? AND userStatus='1' AND doctorStatus='1'
                    GROUP BY doctor, apptime
                ) AS grouped_slots");
        if ($slotStmt) {
            mysqli_stmt_bind_param($slotStmt, "s", $today);
            mysqli_stmt_execute($slotStmt);
            $slotRes = mysqli_stmt_get_result($slotStmt);
            if ($slotRes) {
                $slotRow = mysqli_fetch_assoc($slotRes);
                $slotsBooked = (int)$slotRow['total_slots'];
            }
        }

        $available = max(0, $capacity - $slotsBooked);
        $overbooked = max(0, $slotsBooked - $capacity);

        return array(
            'labels' => array('Booked Slots', 'Available Slots', 'Overbooked'),
            'values' => array($slotsBooked, $available, $overbooked),
            'capacity' => $capacity,
            'active_appointments' => $booked
        );
    }
}

if (!function_exists('hms_create_password_reset_otp')) {
    function hms_create_password_reset_otp($con, $userType, $userIdentifier)
    {
        $otp = (string)random_int(100000, 999999);
        $createdAt = date('Y-m-d H:i:s');
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        $stmt = mysqli_prepare($con, "INSERT INTO password_reset_tokens(user_type,user_identifier,otp_code,expires_at,created_at) VALUES(?,?,?,?,?)");
        if (!$stmt) {
            return null;
        }
        mysqli_stmt_bind_param($stmt, "sssss", $userType, $userIdentifier, $otp, $expiresAt, $createdAt);
        if (!mysqli_stmt_execute($stmt)) {
            return null;
        }
        return $otp;
    }
}

if (!function_exists('hms_user_exists_for_reset')) {
    function hms_user_exists_for_reset($con, $userType, $userIdentifier)
    {
        if ($userType === 'patient') {
            $stmt = mysqli_prepare($con, "SELECT pid FROM patreg WHERE email=? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "s", $userIdentifier);
        } elseif ($userType === 'doctor') {
            $stmt = mysqli_prepare($con, "SELECT username FROM doctb WHERE email=? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "s", $userIdentifier);
        } elseif ($userType === 'admin') {
            $stmt = mysqli_prepare($con, "SELECT username FROM admintb WHERE username=? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "s", $userIdentifier);
        } else {
            return false;
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return $result && mysqli_num_rows($result) === 1;
    }
}

if (!function_exists('hms_get_reset_email_for_user')) {
    function hms_get_reset_email_for_user($con, $userType, $userIdentifier)
    {
        if ($userType === 'patient') {
            $stmt = mysqli_prepare($con, "SELECT email FROM patreg WHERE email=? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "s", $userIdentifier);
        } elseif ($userType === 'doctor') {
            $stmt = mysqli_prepare($con, "SELECT email FROM doctb WHERE email=? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "s", $userIdentifier);
        } elseif ($userType === 'admin') {
            $stmt = mysqli_prepare($con, "SELECT email FROM admintb WHERE username=? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "s", $userIdentifier);
        } else {
            return '';
        }
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            return isset($row['email']) ? trim((string)$row['email']) : '';
        }
        return '';
    }
}

if (!function_exists('hms_reset_password_with_otp')) {
    function hms_reset_password_with_otp($con, $email, $otp, $newPassword)
    {
        return hms_reset_password_with_otp_for_user($con, 'patient', $email, $otp, $newPassword);
    }
}

if (!function_exists('hms_reset_password_with_otp_for_user')) {
    function hms_reset_password_with_otp_for_user($con, $userType, $userIdentifier, $otp, $newPassword)
    {
        $now = date('Y-m-d H:i:s');
        $stmt = mysqli_prepare($con, "SELECT id FROM password_reset_tokens WHERE user_type=? AND user_identifier=? AND otp_code=? AND consumed_at IS NULL AND expires_at>=? ORDER BY id DESC LIMIT 1");
        mysqli_stmt_bind_param($stmt, "ssss", $userType, $userIdentifier, $otp, $now);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (!$result || mysqli_num_rows($result) === 0) {
            return false;
        }
        $row = mysqli_fetch_assoc($result);
        $tokenId = (int)$row['id'];

        $passwordHash = hms_hash_password($newPassword);
        if ($userType === 'patient') {
            $uStmt = mysqli_prepare($con, "UPDATE patreg SET password=?, cpassword=? WHERE email=?");
            mysqli_stmt_bind_param($uStmt, "sss", $passwordHash, $passwordHash, $userIdentifier);
        } elseif ($userType === 'doctor') {
            $uStmt = mysqli_prepare($con, "UPDATE doctb SET password=? WHERE email=?");
            mysqli_stmt_bind_param($uStmt, "ss", $passwordHash, $userIdentifier);
        } elseif ($userType === 'admin') {
            $uStmt = mysqli_prepare($con, "UPDATE admintb SET password=? WHERE username=?");
            mysqli_stmt_bind_param($uStmt, "ss", $passwordHash, $userIdentifier);
        } else {
            return false;
        }

        if (!mysqli_stmt_execute($uStmt) || mysqli_affected_rows($con) < 1) {
            return false;
        }
        $cStmt = mysqli_prepare($con, "UPDATE password_reset_tokens SET consumed_at=? WHERE id=?");
        mysqli_stmt_bind_param($cStmt, "si", $now, $tokenId);
        mysqli_stmt_execute($cStmt);
        return true;
    }
}

if (!function_exists('hms_admin_reset_patient_password')) {
    function hms_admin_reset_patient_password($con, $email, $newPassword)
    {
        $passwordHash = hms_hash_password($newPassword);
        $stmt = mysqli_prepare($con, "UPDATE patreg SET password=?, cpassword=? WHERE email=?");
        mysqli_stmt_bind_param($stmt, "sss", $passwordHash, $passwordHash, $email);
        return mysqli_stmt_execute($stmt) && mysqli_affected_rows($con) > 0;
    }
}

if (isset($con) && $con) {
    hms_ensure_schema($con);
    hms_enforce_csrf_on_post();
}
?>
