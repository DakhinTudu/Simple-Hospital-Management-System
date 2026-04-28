<?php
include('include/config.php');
include('include/security.php');
include('include/mailer.php');

$message = '';
$selectedType = isset($_POST['user_type']) ? hms_clean_input($_POST['user_type']) : 'patient';
$selectedType = in_array($selectedType, array('patient', 'doctor', 'admin'), true) ? $selectedType : 'patient';

function hms_reset_identifier_label($userType)
{
    if ($userType === 'admin') {
        return 'Admin Username';
    }
    return 'Email';
}

if (isset($_POST['request_otp'])) {
    $identifier = hms_clean_input($_POST['identifier']);
    $isValidIdentifier = $selectedType === 'admin' ? $identifier !== '' : hms_is_valid_email($identifier);
    if ($isValidIdentifier && hms_user_exists_for_reset($con, $selectedType, $identifier)) {
        $toEmail = hms_get_reset_email_for_user($con, $selectedType, $identifier);
        if ($toEmail === '' || !hms_is_valid_email($toEmail)) {
            $message = 'No email is configured for this account. Please contact support.';
        } else {
            $otp = hms_create_password_reset_otp($con, $selectedType, $identifier);
            if ($otp !== null && hms_send_otp_email($toEmail, $otp, $selectedType)) {
                $message = 'OTP sent to your registered email address.';
                hms_audit_log($con, 'password.otp_generated', $selectedType, $identifier);
            } else {
                $message = 'Unable to send OTP email right now. Please try again.';
            }
        }
    } else {
        $message = 'Account not found or invalid identifier.';
    }
}

if (isset($_POST['reset_with_otp'])) {
    $identifier = hms_clean_input($_POST['identifier']);
    $otp = hms_clean_input($_POST['otp']);
    $password = hms_clean_input($_POST['new_password']);
    if (hms_reset_password_with_otp_for_user($con, $selectedType, $identifier, $otp, $password)) {
        hms_audit_log($con, 'password.reset_with_otp', $selectedType, $identifier);
        $message = 'Password reset successful. You can login now.';
    } else {
        $message = 'Invalid/expired OTP or account details.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Global Hospitals</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans:300,400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/public-site.css">
</head>
<body class="public-site">
    <?php include('include/public-header.php'); ?>

    <!-- Mini Hero -->
    <section class="mini-hero" style="background: linear-gradient(135deg, var(--primary-blue), var(--accent-cyan)); padding: 120px 0 60px;">
        <div class="container text-center text-white">
            <span class="section-subtitle text-white-50">Security Check</span>
            <h1 class="display-4 font-weight-bold">Account Recovery</h1>
            <p class="lead opacity-75">Recover your access using a secure one-time password sent to your email.</p>
        </div>
    </section>

    <section class="public-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="public-card p-4 p-md-5 border-0 shadow-sm">
                        <?php if ($message !== '') { ?>
                            <div class="alert alert-info border-0 shadow-sm mb-4"><?php echo hms_esc($message); ?></div>
                        <?php } ?>

                        <div class="row">
                            <div class="col-md-5 border-right">
                                <h5 class="font-weight-bold mb-4 text-primary">1. Select Account Type</h5>
                                <form method="post">
                                    <?php echo hms_csrf_field(); ?>
                                    <div class="form-group">
                                        <select name="user_type" class="form-control" onchange="this.form.submit()">
                                            <option value="patient" <?php echo $selectedType === 'patient' ? 'selected' : ''; ?>>Patient Account</option>
                                            <option value="doctor" <?php echo $selectedType === 'doctor' ? 'selected' : ''; ?>>Doctor Account</option>
                                            <option value="admin" <?php echo $selectedType === 'admin' ? 'selected' : ''; ?>>Staff Account</option>
                                        </select>
                                    </div>
                                    <p class="small text-muted mt-4">Choose the type of account you wish to recover.</p>
                                </form>
                            </div>

                            <div class="col-md-7 pl-md-5">
                                <form method="post">
                                    <?php echo hms_csrf_field(); ?>
                                    <input type="hidden" name="user_type" value="<?php echo hms_esc($selectedType); ?>">
                                    <h5 class="font-weight-bold mb-4">2. Request OTP</h5>
                                    <div class="form-group mb-4">
                                        <label class="small font-weight-bold text-muted"><?php echo strtoupper(hms_reset_identifier_label($selectedType)); ?></label>
                                        <input type="<?php echo $selectedType === 'admin' ? 'text' : 'email'; ?>" name="identifier" class="form-control" placeholder="Enter your <?php echo hms_reset_identifier_label($selectedType); ?>" required>
                                    </div>
                                    <button type="submit" name="request_otp" class="btn btn-primary-custom w-100">Send Recovery OTP</button>
                                </form>

                                <hr class="my-5">

                                <form method="post">
                                    <?php echo hms_csrf_field(); ?>
                                    <input type="hidden" name="user_type" value="<?php echo hms_esc($selectedType); ?>">
                                    <h5 class="font-weight-bold mb-4">3. Reset Password</h5>
                                    <div class="form-group mb-4">
                                        <label class="small font-weight-bold text-muted"><?php echo strtoupper(hms_reset_identifier_label($selectedType)); ?></label>
                                        <input type="<?php echo $selectedType === 'admin' ? 'text' : 'email'; ?>" name="identifier" class="form-control" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="small font-weight-bold text-muted">OTP CODE</label>
                                                <input type="text" name="otp" class="form-control" placeholder="Enter OTP" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-4">
                                                <label class="small font-weight-bold text-muted">NEW PASSWORD</label>
                                                <input type="password" name="new_password" class="form-control" placeholder="New Password" required>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" name="reset_with_otp" class="btn btn-primary-custom w-100">Update Secure Password</button>
                                </form>
                                <div class="text-center mt-4">
                                    <a href="index.php" class="small font-weight-bold text-muted"><i class="fa fa-arrow-left mr-2"></i> Back to Login</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include('include/public-footer.php'); ?>
</body>
</html>
