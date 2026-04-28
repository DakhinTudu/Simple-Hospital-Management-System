<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error | Global Hospitals</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.png" />
    <link href="https://fonts.googleapis.com/css?family=IBM+Plex+Sans:300,400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/public-site.css">
</head>
<body class="public-site">
    <?php include('include/public-header.php'); ?>
    <section class="mini-hero" style="background: linear-gradient(135deg, var(--primary-blue), var(--accent-cyan)); padding: 150px 0;">
        <div class="container text-center text-white">
            <div class="card-icon-box mx-auto mb-4" style="width: 80px; height: 80px; font-size: 2rem; background: rgba(255,255,255,0.2); color: #fff;">
                <i class="fa fa-exclamation-triangle"></i>
            </div>
            <h1 class="display-4 font-weight-bold">Authentication Failed</h1>
            <p class="lead opacity-75 mb-5">Invalid credentials. Please verify your details and try again.</p>
            <a href="index.php" class="btn btn-outline-light btn-lg px-5">Back to Login</a>
        </div>
    </section>
    <?php include('include/public-footer.php'); ?>
</body>
</html>
