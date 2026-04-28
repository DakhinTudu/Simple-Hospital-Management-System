<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments | Global Hospitals</title>
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
            <span class="section-subtitle text-white-50">Specialized Units</span>
            <h1 class="display-4 font-weight-bold">Clinical Departments</h1>
            <p class="lead opacity-75">Integrated specialty units with evidence-based care and modern infrastructure.</p>
        </div>
    </section>

    <!-- Departments Grid -->
    <section class="public-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="public-card p-4 h-100 border-0 shadow-sm">
                        <div class="card-icon-box mb-3"><i class="fa fa-heartbeat"></i></div>
                        <h5>Cardiology</h5>
                        <p class="text-muted small mb-0">Advanced heart care with preventive screening, diagnostics, and intervention support.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="public-card p-4 h-100 border-0 shadow-sm">
                        <div class="card-icon-box mb-3"><i class="fa fa-user-md"></i></div>
                        <h5>General Medicine</h5>
                        <p class="text-muted small mb-0">Primary adult care for routine, chronic, and acute conditions.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="public-card p-4 h-100 border-0 shadow-sm">
                        <div class="card-icon-box mb-3"><i class="fa fa-plus-square"></i></div>
                        <h5>Neurology</h5>
                        <p class="text-muted small mb-0">Evaluation and treatment for stroke, seizures, headache, and nerve disorders.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="public-card p-4 h-100 border-0 shadow-sm">
                        <div class="card-icon-box mb-3"><i class="fa fa-child"></i></div>
                        <h5>Pediatrics</h5>
                        <p class="text-muted small mb-0">Comprehensive child healthcare from preventive checkups to specialized care.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="public-card p-4 h-100 border-0 shadow-sm">
                        <div class="card-icon-box mb-3"><i class="fa fa-flask"></i></div>
                        <h5>Laboratory</h5>
                        <p class="text-muted small mb-0">Fast and accurate pathology support for outpatient and inpatient services.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="public-card p-4 h-100 border-0 shadow-sm">
                        <div class="card-icon-box mb-3"><i class="fa fa-wheelchair"></i></div>
                        <h5>Rehabilitation</h5>
                        <p class="text-muted small mb-0">Physio and recovery programs for post-surgery and mobility improvement.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include('include/public-footer.php'); ?>
</body>
</html>

