<?php
include('include/config.php');
include('include/security.php');
$publicTrend = hms_get_appointments_trend($con, 7);
$publicToday = hms_get_daily_analytics($con);
$weeklyBookings = array_sum($publicTrend['bookings']);
$avgDailyBookings = count($publicTrend['bookings']) > 0 ? round($weeklyBookings / count($publicTrend['bookings']), 1) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Hospitals Bhubaneswar | Best Healthcare in Odisha</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans:300,400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/public-site.css?v=<?php echo time(); ?>">

    <style>
        .register-left img {
            width: 100px;
            margin-bottom: 20px;
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
            border-radius: 10px;
            padding: 12px 15px;
            height: auto;
            border: 1px solid #e2e8f0;
        }
        .register-heading {
            font-weight: 700;
            margin-bottom: 30px;
            color: var(--dark-navy);
        }
    </style>

    <script>
        var check = function() {
            if (document.getElementById('password').value == document.getElementById('cpassword').value) {
                document.getElementById('message').style.color = '#10b981';
                document.getElementById('message').innerHTML = '<i class="fa fa-check-circle"></i> Matched';
            } else {
                document.getElementById('message').style.color = '#ef4444';
                document.getElementById('message').innerHTML = '<i class="fa fa-times-circle"></i> Not Matching';
            }
        }

        function alphaOnly(event) {
            var key = event.keyCode;
            return ((key >= 65 && key <= 90) || key == 8 || key == 32);
        };

        function checklen() {
            var pass1 = document.getElementById("password");  
            if(pass1.value.length < 6){  
                alert("Password must be at least 6 characters long. Try again!");  
                return false;  
            }  
        }
    </script>
</head>

<body class="public-site">
    <?php include('include/public-header.php'); ?>

    <!-- Hero Section -->
    <header class="hero-wrapper">
        <div class="hero-bg-image" style="background-image: url('modern_hospital_exterior_1777359681142.png');"></div>
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <div class="row">
                <div class="col-lg-8">
                    <span class="section-subtitle text-white-50">Welcome to Global Hospitals Bhubaneswar</span>
                    <h1>Modern Healthcare, Compassionate Service</h1>
                    <p>Experience world-class medical care in the heart of Odisha. Our team of expert doctors and state-of-the-art facilities in Patia are dedicated to your well-being.</p>
                    <div class="mt-4">
                        <a href="#auth-section" class="btn btn-primary-custom mr-3">Book Appointment</a>
                        <a href="services.php" class="btn btn-outline-light py-3 px-4" style="border-radius: 12px; font-weight: 600;">Our Services</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Services Section -->
    <section class="public-section bg-transparent">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-subtitle">Our Excellence</span>
                <h2 class="section-title">Specialized Healthcare Services</h2>
                <p class="text-muted">We provide a wide range of medical services to meet your needs.</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="public-card p-4 h-100">
                        <div class="card-icon-box"><i class="fa fa-stethoscope"></i></div>
                        <h4>Expert Consultations</h4>
                        <p class="text-muted">Connect with top-rated specialists for personalized medical advice and treatment plans.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="public-card p-4 h-100">
                        <div class="card-icon-box"><i class="fa fa-heartbeat"></i></div>
                        <h4>Cardiac Care</h4>
                        <p class="text-muted">Advanced heart care services including diagnostics, surgery, and rehabilitation.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="public-card p-4 h-100">
                        <div class="card-icon-box"><i class="fa fa-flask"></i></div>
                        <h4>Modern Diagnostics</h4>
                        <p class="text-muted">Equipped with the latest technology for accurate and rapid diagnostic results.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Auth Section (Registration/Login) -->
    <section class="public-section bg-transparent" id="auth-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 mb-5 mb-lg-0">
                    <span class="section-subtitle">Get Started</span>
                    <h2 class="section-title">Your Journey to Better Health Starts Here</h2>
                    <p class="lead text-muted mb-4">Join thousands of patients who trust Global Hospitals for their healthcare needs.</p>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="fa fa-check-circle text-primary mr-2"></i> Easy Online Appointments</li>
                        <li class="mb-3"><i class="fa fa-check-circle text-primary mr-2"></i> Access to Medical History</li>
                        <li class="mb-3"><i class="fa fa-check-circle text-primary mr-2"></i> Direct Doctor Communication</li>
                    </ul>
                </div>
                <div class="col-lg-7">
                    <div class="auth-section">
                        <ul class="nav nav-tabs auth-tabs-nav nav-justified" id="myTab" role="tablist">
                             <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab">Patient Register</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="login-tab" data-toggle="tab" href="#login" role="tab">Patient Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab">Doctor Portal</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="admin-tab" data-toggle="tab" href="#admin" role="tab">Receptionist</a>
                            </li>
                        </ul>
                        <div class="tab-content p-4 p-md-5" id="myTabContent">
                            <!-- Patient Registration -->
                            <div class="tab-pane fade show active" id="home" role="tabpanel">
                                <h3 class="register-heading">Create Patient Account</h3>
                                <form method="post" action="patient-registration.php">
                                    <?php echo hms_csrf_field(); ?>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <input type="text" class="form-control" placeholder="First Name *" name="fname" onkeydown="return alphaOnly(event);" required/>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <input type="text" class="form-control" placeholder="Last Name *" name="lname" onkeydown="return alphaOnly(event);" required/>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <input type="email" class="form-control" placeholder="Your Email *" name="email" required/>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <input type="tel" minlength="10" maxlength="10" name="contact" class="form-control" placeholder="Phone Number *" required/>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <input type="password" class="form-control" placeholder="Password *" id="password" name="password" onkeyup='check();' required/>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <input type="password" class="form-control" id="cpassword" placeholder="Confirm Password *" name="cpassword" onkeyup='check();' required/>
                                            <small id='message' class="mt-1 d-block"></small>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <div class="py-2">
                                                <label class="mr-3"><input type="radio" name="gender" value="Male" checked> Male</label>
                                                <label><input type="radio" name="gender" value="Female"> Female</label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <input type="submit" class="btnRegister" name="patsub1" onclick="return checklen();" value="Create Account"/>
                                            <div class="text-center mt-3">
                                                <a href="#login" class="text-primary font-weight-600" onclick="$('#login-tab').tab('show');">Already have an account? Login here</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Patient Login -->
                            <div class="tab-pane fade" id="login" role="tabpanel">
                                <h3 class="register-heading">Patient Login</h3>
                                <form method="post" action="patient-auth.php">
                                    <?php echo hms_csrf_field(); ?>
                                    <div class="form-group mb-4">
                                        <input type="email" class="form-control" placeholder="Email ID *" name="email" required/>
                                    </div>
                                    <div class="form-group mb-4">
                                        <input type="password" class="form-control" placeholder="Password *" name="password2" required/>
                                    </div>
                                    <input type="submit" class="btnRegister" name="patsub" value="Login to Portal"/>
                                    <div class="text-center mt-3">
                                        <a href="forgot-password.php" class="text-muted small">Forgot password?</a>
                                    </div>
                                </form>
                            </div>

                            <!-- Doctor Login -->
                            <div class="tab-pane fade" id="profile" role="tabpanel">
                                <h3 class="register-heading">Doctor Portal Login</h3>
                                <form method="post" action="doctor-auth.php">
                                    <?php echo hms_csrf_field(); ?>
                                    <div class="form-group mb-4">
                                        <input type="text" class="form-control" placeholder="Username *" name="username3" required/>
                                    </div>
                                    <div class="form-group mb-4">
                                        <input type="password" class="form-control" placeholder="Password *" name="password3" required/>
                                    </div>
                                    <input type="submit" class="btnRegister" name="docsub1" value="Login to Portal"/>
                                    <div class="text-center mt-3">
                                        <a href="forgot-password.php" class="text-muted small">Forgot password?</a>
                                    </div>
                                </form>
                            </div>

                            <!-- Admin Login -->
                            <div class="tab-pane fade" id="admin" role="tabpanel">
                                <h3 class="register-heading">Staff Login</h3>
                                <form method="post" action="admin-auth.php">
                                    <?php echo hms_csrf_field(); ?>
                                    <div class="form-group mb-4">
                                        <input type="text" class="form-control" placeholder="Admin Username *" name="username1" required/>
                                    </div>
                                    <div class="form-group mb-4">
                                        <input type="password" class="form-control" placeholder="Password *" name="password2" required/>
                                    </div>
                                    <input type="submit" class="btnRegister" name="adsub" value="Access Dashboard"/>
                                    <div class="text-center mt-3">
                                        <a href="forgot-password.php" class="text-muted small">Forgot password?</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Analytics Section -->
    <section class="public-section" style="background-color: #f8fafc;">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-subtitle">Real-time Insights</span>
                <h2 class="section-title">Daily Hospital Activity</h2>
                <p class="text-muted">Transparent overview of our daily clinical operations.</p>
            </div>
            
            <div class="row mb-5">
                <div class="col-md-4 mb-4">
                    <div class="stat-item public-card">
                        <span class="stat-number"><?php echo (int)$publicToday['appointments_today']; ?></span>
                        <span class="stat-label">Bookings Today</span>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="stat-item public-card">
                        <span class="stat-number"><?php echo (int)$weeklyBookings; ?></span>
                        <span class="stat-label">This Week</span>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="stat-item public-card">
                        <span class="stat-number"><?php echo hms_esc($avgDailyBookings); ?></span>
                        <span class="stat-label">Avg. Daily Bookings</span>
                    </div>
                </div>
            </div>

            <div class="public-card p-4">
                <div style="height:350px;">
                    <canvas id="publicBookingTrendChart"></canvas>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Analytics Chart
        (function () {
            var trendData = <?php echo json_encode($publicTrend); ?>;
            var chartEl = document.getElementById('publicBookingTrendChart');
            if (!chartEl) return;
            
            new Chart(chartEl, {
                type: 'line',
                data: {
                    labels: trendData.labels,
                    datasets: [{
                        label: 'Bookings',
                        data: trendData.bookings,
                        borderColor: '#1e40af',
                        backgroundColor: 'rgba(30, 64, 175, 0.1)',
                        pointBackgroundColor: '#1e40af',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { font: { family: 'IBM Plex Sans' } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'IBM Plex Sans' } }
                        }
                    }
                }
            });
        })();
    </script>
    <?php include('include/public-footer.php'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle external links to specific tabs
            var hash = window.location.hash;
            if (hash === '#login' || hash === '#profile' || hash === '#admin') {
                $('.auth-tabs-nav a[href="' + hash + '"]').tab('show');
            }

            // Sync navbar Login button to jump to auth section
            $('a[href="index.php#auth-section"]').on('click', function(e) {
                if (window.location.pathname.indexOf('index.php') !== -1 || window.location.pathname === '/') {
                    e.preventDefault();
                    document.getElementById('auth-section').scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>


  
