<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Guide | Global Hospitals</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans:300,400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/public-site.css">
    <style>
        .guide-list {
            list-style: none;
            padding-left: 0;
        }
        .guide-list li {
            position: relative;
            padding-left: 30px;
            margin-bottom: 15px;
            color: var(--text-main);
        }
        .guide-list li::before {
            content: "\f058";
            font-family: FontAwesome;
            position: absolute;
            left: 0;
            color: var(--primary-blue);
            font-size: 1.1rem;
        }
        .guide-card-head {
            background: #f8fafc;
            border-bottom: 1px solid #edf2f7;
            padding: 20px 24px;
            border-radius: 20px 20px 0 0;
        }
    </style>
</head>
<body class="public-site">
    <?php include('include/public-header.php'); ?>

    <!-- Mini Hero -->
    <section class="mini-hero" style="background: linear-gradient(135deg, var(--primary-blue), var(--accent-cyan)); padding: 120px 0 60px;">
        <div class="container text-center text-white">
            <span class="section-subtitle text-white-50">Helping You Prepare</span>
            <h1 class="display-4 font-weight-bold">Patient Guide</h1>
            <p class="lead opacity-75">Everything you need before visiting, during treatment, and after discharge.</p>
        </div>
    </section>

    <!-- Guide Sections -->
    <section class="public-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="public-card h-100 border-0 shadow-sm overflow-hidden">
                        <div class="guide-card-head">
                            <h5 class="mb-0 font-weight-bold text-primary">Before Your Visit</h5>
                        </div>
                        <div class="p-4">
                            <ul class="guide-list mb-0">
                                <li>Carry government ID and previous medical records.</li>
                                <li>Arrive at least 20 minutes before scheduled appointment.</li>
                                <li>Carry current medication list and allergy details.</li>
                                <li>For fasting tests, follow instructions sent by care team.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="public-card h-100 border-0 shadow-sm overflow-hidden">
                        <div class="guide-card-head">
                            <h5 class="mb-0 font-weight-bold text-primary">During Consultation</h5>
                        </div>
                        <div class="p-4">
                            <ul class="guide-list mb-0">
                                <li>Clearly explain symptoms and medical history.</li>
                                <li>Ask questions about diagnosis, treatment, and side effects.</li>
                                <li>Confirm follow-up date and test schedule.</li>
                                <li>Use online appointment portal for updates and slots.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 mt-2">
                    <div class="public-card border-0 shadow-sm overflow-hidden">
                        <div class="guide-card-head">
                            <h5 class="mb-0 font-weight-bold text-primary">Discharge & Follow-up</h5>
                        </div>
                        <div class="p-4">
                            <div class="row">
                                <div class="col-md-4 mb-4 mb-md-0">
                                    <div class="card-icon-box mb-3" style="width: 40px; height: 40px; font-size: 1rem;"><i class="fa fa-medkit"></i></div>
                                    <h6 class="font-weight-bold">Medicines</h6>
                                    <p class="text-muted small mb-0">Follow e-prescription exactly and report side effects immediately.</p>
                                </div>
                                <div class="col-md-4 mb-4 mb-md-0">
                                    <div class="card-icon-box mb-3" style="width: 40px; height: 40px; font-size: 1rem;"><i class="fa fa-file-text-o"></i></div>
                                    <h6 class="font-weight-bold">Reports</h6>
                                    <p class="text-muted small mb-0">Keep diagnostic and discharge reports safely for future consultations.</p>
                                </div>
                                <div class="col-md-4">
                                    <div class="card-icon-box mb-3" style="width: 40px; height: 40px; font-size: 1rem; background: #fee2e2; color: #ef4444;"><i class="fa fa-phone"></i></div>
                                    <h6 class="font-weight-bold">Emergency</h6>
                                    <p class="text-muted small mb-0">For urgent issues, call emergency support at +91 99999 99999.</p>
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

