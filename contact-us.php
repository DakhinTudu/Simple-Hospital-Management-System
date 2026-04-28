<?php include('include/security.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Global Hospitals</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans:300,400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/public-site.css">
    <style>
        .contact-shell {
            background: #fff;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
        }
        .contact-info-pane {
            background: linear-gradient(135deg, var(--primary-blue), var(--accent-cyan));
            color: #fff;
            padding: 50px;
            height: 100%;
        }
        .contact-form-pane {
            padding: 50px;
        }
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }
        .info-item i {
            width: 45px;
            height: 45px;
            background: rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            margin-right: 20px;
            font-size: 1.2rem;
        }
    </style>
</head>
<body class="public-site">
    <?php include('include/public-header.php'); ?>

    <!-- Mini Hero -->
    <section class="mini-hero" style="background: linear-gradient(135deg, var(--primary-blue), var(--accent-cyan)); padding: 120px 0 60px;">
        <div class="container text-center text-white">
            <span class="section-subtitle text-white-50">Reach Out To Us</span>
            <h1 class="display-4 font-weight-bold">Contact Us</h1>
            <p class="lead opacity-75">Have a medical or appointment query? Our care team will get back to you quickly.</p>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="public-section">
        <div class="container">
            <div class="contact-shell">
                <div class="row no-gutters">
                    <div class="col-lg-5">
                        <div class="contact-info-pane">
                            <h3 class="font-weight-bold mb-4">Get In Touch</h3>
                            <p class="opacity-75 mb-5">We are available 24/7 for emergency support and during business hours for regular consultation queries.</p>
                            
                            <div class="info-item">
                                <i class="fa fa-phone"></i>
                                <div>
                                    <h6 class="mb-0 font-weight-bold">Call Us</h6>
                                    <p class="mb-0 small">+91 674 2725 123</p>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="fa fa-envelope-o"></i>
                                <div>
                                    <h6 class="mb-0 font-weight-bold">Email Us</h6>
                                    <p class="mb-0 small">care@globalhospitals.in</p>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="fa fa-map-marker"></i>
                                <div>
                                    <h6 class="mb-0 font-weight-bold">Our Location</h6>
                                    <p class="mb-0 small">Plot No. 12, KIIT Road, Patia, Bhubaneswar, 751024</p>
                                </div>
                            </div>

                            <!-- Map Integration -->
                            <div class="mt-5 rounded-lg overflow-hidden shadow-sm" style="height: 200px; border: 1px solid rgba(255,255,255,0.2);">
                                <iframe 
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3740.7673238647714!2d85.81594967523827!3d20.351272710689943!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a19091609148c77%3A0x633900222a7f5a9b!2sPatia%2C%20Bhubaneswar%2C%20Odisha!5e0!3m2!1sen!2sin!4v1714300000000!5m2!1sen!2sin" 
                                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                            </div>
                            
                            <hr style="border-color: rgba(255,255,255,0.1); margin: 40px 0;">
                            
                            <div class="mt-4">
                                <h6 class="font-weight-bold mb-3">Working Hours</h6>
                                <p class="small opacity-75 mb-1">Mon - Sat: 8:00 AM to 10:00 PM</p>
                                <p class="small opacity-75">Sunday: Emergency Services Only</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="contact-form-pane">
                            <h3 class="font-weight-bold mb-4 text-dark">Send us a message</h3>
                            <form method="post" action="contact.php">
                                <?php echo hms_csrf_field(); ?>
                                <div class="row">
                                    <div class="col-md-6 mb-4">
                                        <div class="form-group mb-0">
                                            <label class="small font-weight-bold text-muted">FULL NAME</label>
                                            <input type="text" name="txtName" class="form-control" placeholder="John Doe" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <div class="form-group mb-0">
                                            <label class="small font-weight-bold text-muted">EMAIL ADDRESS</label>
                                            <input type="email" name="txtEmail" class="form-control" placeholder="john@example.com" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-4">
                                        <div class="form-group mb-0">
                                            <label class="small font-weight-bold text-muted">PHONE NUMBER</label>
                                            <input type="tel" name="txtPhone" class="form-control" placeholder="10-digit number" minlength="10" maxlength="10" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-4">
                                        <div class="form-group mb-0">
                                            <label class="small font-weight-bold text-muted">YOUR MESSAGE</label>
                                            <textarea name="txtMsg" class="form-control" placeholder="How can we help you?" rows="5" required></textarea>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" name="btnSubmit" class="btn btn-primary-custom w-100 py-3">Send Message</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include('include/public-footer.php'); ?>
</body>
</html>




