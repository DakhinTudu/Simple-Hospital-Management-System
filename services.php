<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Global Hospitals</title>
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
            <span class="section-subtitle text-white-50">Our Mission</span>
            <h1 class="display-4 font-weight-bold">Clinical Excellence in Bhubaneswar</h1>
            <p class="lead opacity-75">Global Hospitals Bhubaneswar is dedicated to serving the community of Odisha with state-of-the-art medical technology and compassionate care.</p>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="public-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <span class="section-subtitle">The Global Advantage</span>
                    <h2 class="section-title">Why Patients Trust Us</h2>
                    <p class="text-muted mb-4">We combine cutting-edge technology with a patient-centered approach to deliver the best possible outcomes.</p>
                    <div class="row">
                        <div class="col-sm-6 mb-4">
                            <div class="public-card p-3 border-0 shadow-sm">
                                <div class="card-icon-box mb-3" style="width: 40px; height: 40px; font-size: 1rem;"><i class="fa fa-stethoscope"></i></div>
                                <h6>Expert Doctors</h6>
                                <p class="small text-muted mb-0">Consultants with decades of experience.</p>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-4">
                            <div class="public-card p-3 border-0 shadow-sm">
                                <div class="card-icon-box mb-3" style="width: 40px; height: 40px; font-size: 1rem;"><i class="fa fa-heartbeat"></i></div>
                                <h6>Emergency Care</h6>
                                <p class="small text-muted mb-0">24/7 rapid response critical care.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="img/dummy/img-1.png" alt="Hospital care" class="img-fluid rounded-lg shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <!-- Our Clinical Services -->
    <section class="public-section bg-transparent">
        <div class="container">
            <div class="text-center mb-5">
                <span class="section-subtitle">What We Offer</span>
                <h2 class="section-title">Our Key Clinical Services</h2>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="public-card p-4 h-100">
                        <i class="fa fa-check-circle text-primary mb-3 fa-2x"></i>
                        <h5>General Medicine</h5>
                        <p class="text-muted small">Comprehensive care for a wide range of health issues and preventive checkups.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="public-card p-4 h-100">
                        <i class="fa fa-heart text-danger mb-3 fa-2x"></i>
                        <h5>Cardiology</h5>
                        <p class="text-muted small">Expert heart care including diagnostics, surgery, and post-op care.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="public-card p-4 h-100">
                        <i class="fa fa-brain text-info mb-3 fa-2x"></i>
                        <h5>Neurology</h5>
                        <p class="text-muted small">Advanced treatment for neurological disorders and brain health.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include('include/public-footer.php'); ?>
</body>
</html>

