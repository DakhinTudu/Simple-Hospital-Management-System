<?php
include('include/config.php');
include('include/security.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Portal | Global Hospitals</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans:300,400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/public-site.css?v=<?php echo time(); ?>">

    <style>
        .staff-bg {
            background-color: #f4f7fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
        }
        .login-header {
            background: var(--dark-navy);
            color: #fff;
            padding: 30px;
            text-align: center;
        }
        .login-header .fa {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .nav-tabs.auth-tabs-nav {
            border-bottom: 1px solid #e2e8f0;
            background: #fafbfc;
        }
        .nav-tabs.auth-tabs-nav .nav-link {
            border: none;
            color: #64748b;
            font-weight: 500;
            padding: 15px;
        }
        .nav-tabs.auth-tabs-nav .nav-link.active {
            color: var(--primary-blue);
            border-bottom: 3px solid var(--primary-blue);
            background: transparent;
        }
        .btnRegister {
            background: var(--primary-blue);
            color: #fff;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        .btnRegister:hover {
            background: var(--secondary-blue);
            transform: translateY(-2px);
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            height: auto;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
        }
        .form-control:focus {
            background-color: #fff;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.2rem rgba(30, 64, 175, 0.25);
        }
    </style>
</head>

<body class="staff-bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 d-flex justify-content-center">
                <div class="login-box">
                    <div class="login-header">
                        <i class="fa fa-hospital-o"></i>
                        <h4 class="mb-0">Global Hospitals</h4>
                        <p class="mb-0 small text-white-50">Authorized Personnel Only</p>
                    </div>
                    
                    <ul class="nav nav-tabs auth-tabs-nav nav-justified" id="staffTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="doctor-tab" data-toggle="tab" href="#doctor" role="tab">Doctor</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="admin-tab" data-toggle="tab" href="#admin" role="tab">Admin</a>
                        </li>
                    </ul>
                    
                    <div class="tab-content p-4 p-md-5" id="staffTabContent">
                        <!-- Doctor Login -->
                        <div class="tab-pane fade show active" id="doctor" role="tabpanel">
                            <form method="post" action="doctor-auth.php">
                                <?php echo hms_csrf_field(); ?>
                                <div class="form-group mb-4">
                                    <label class="small text-muted font-weight-600 mb-2">USERNAME</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i class="fa fa-user text-muted"></i></span>
                                        </div>
                                        <input type="text" class="form-control border-left-0 pl-0" placeholder="Doctor Username" name="username3" required/>
                                    </div>
                                </div>
                                <div class="form-group mb-4">
                                    <label class="small text-muted font-weight-600 mb-2">PASSWORD</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i class="fa fa-lock text-muted"></i></span>
                                        </div>
                                        <input type="password" class="form-control border-left-0 pl-0" placeholder="Password" name="password3" required/>
                                    </div>
                                </div>
                                <input type="submit" class="btnRegister" name="docsub1" value="Secure Login"/>
                                <div class="text-center mt-3">
                                    <a href="forgot-password.php" class="text-muted small">Forgot password?</a>
                                </div>
                            </form>
                        </div>

                        <!-- Admin Login -->
                        <div class="tab-pane fade" id="admin" role="tabpanel">
                            <form method="post" action="admin-auth.php">
                                <?php echo hms_csrf_field(); ?>
                                <div class="form-group mb-4">
                                    <label class="small text-muted font-weight-600 mb-2">ADMIN ID</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i class="fa fa-shield text-muted"></i></span>
                                        </div>
                                        <input type="text" class="form-control border-left-0 pl-0" placeholder="Admin Username" name="username1" required/>
                                    </div>
                                </div>
                                <div class="form-group mb-4">
                                    <label class="small text-muted font-weight-600 mb-2">PASSWORD</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-light border-right-0"><i class="fa fa-lock text-muted"></i></span>
                                        </div>
                                        <input type="password" class="form-control border-left-0 pl-0" placeholder="Password" name="password2" required/>
                                    </div>
                                </div>
                                <input type="submit" class="btnRegister bg-dark" name="adsub" value="Access Dashboard"/>
                                <div class="text-center mt-3">
                                    <a href="forgot-password.php" class="text-muted small">Forgot password?</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 text-center mt-4">
                <a href="index.php" class="text-muted small"><i class="fa fa-arrow-left mr-1"></i> Return to Main Website</a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
