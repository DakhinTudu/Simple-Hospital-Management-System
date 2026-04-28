<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors | Global Hospitals</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans:300,400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/public-site.css">
    <style>
        .team-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: all 0.4s ease;
        }
        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .team-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: transform 0.6s ease;
        }
        .team-card:hover img {
            transform: scale(1.05);
        }
        .team-card .card-body {
            padding: 24px;
            background: #fff;
        }
        .badge-soft {
            background: #eff6ff;
            color: var(--primary-blue);
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }
    </style>
</head>
<body class="public-site">
    <?php include('include/public-header.php'); ?>

    <!-- Mini Hero -->
    <section class="mini-hero" style="background: linear-gradient(135deg, var(--primary-blue), var(--accent-cyan)); padding: 120px 0 60px;">
        <div class="container text-center text-white">
            <span class="section-subtitle text-white-50">Our Experts</span>
            <h1 class="display-4 font-weight-bold">Meet Our Specialists</h1>
            <p class="lead opacity-75">Experienced consultants delivering trusted care across specialties.</p>
        </div>
    </section>

    <!-- Doctors Grid -->
    <section class="public-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="team-card">
                        <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?auto=format&fit=crop&w=800&q=80" alt="Doctor 1">
                        <div class="card-body text-center">
                            <span class="badge-soft">Cardiology</span>
                            <h4 class="mt-3 mb-1 font-weight-bold">Dr. Ashok Goyal</h4>
                            <p class="text-muted small mb-3">Senior Consultant, Heart & Vascular Care</p>
                            <div class="social-links mt-2">
                                <a href="#" class="btn btn-sm btn-light rounded-circle mx-1"><i class="fa fa-linkedin"></i></a>
                                <a href="#" class="btn btn-sm btn-light rounded-circle mx-1"><i class="fa fa-twitter"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="team-card">
                        <img src="https://images.unsplash.com/photo-1594824476967-48c8b964273f?auto=format&fit=crop&w=800&q=80" alt="Doctor 2">
                        <div class="card-body text-center">
                            <span class="badge-soft">Pediatrics</span>
                            <h4 class="mt-3 mb-1 font-weight-bold">Dr. Ganesh Kumar</h4>
                            <p class="text-muted small mb-3">Child Specialist & Preventive Pediatric Care</p>
                            <div class="social-links mt-2">
                                <a href="#" class="btn btn-sm btn-light rounded-circle mx-1"><i class="fa fa-linkedin"></i></a>
                                <a href="#" class="btn btn-sm btn-light rounded-circle mx-1"><i class="fa fa-twitter"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="team-card">
                        <img src="https://images.unsplash.com/photo-1651008376811-b90baee60c1f?auto=format&fit=crop&w=800&q=80" alt="Doctor 3">
                        <div class="card-body text-center">
                            <span class="badge-soft">Neurology</span>
                            <h4 class="mt-3 mb-1 font-weight-bold">Dr. Abbis R.</h4>
                            <p class="text-muted small mb-3">Consultant Neurologist, Stroke & Neuro Rehab</p>
                            <div class="social-links mt-2">
                                <a href="#" class="btn btn-sm btn-light rounded-circle mx-1"><i class="fa fa-linkedin"></i></a>
                                <a href="#" class="btn btn-sm btn-light rounded-circle mx-1"><i class="fa fa-twitter"></i></a>
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

