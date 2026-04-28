<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top public-nav" id="mainNav">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fa fa-heartbeat mr-2"></i> GLOBAL HOSPITALS
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#publicNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="publicNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>"><a class="nav-link" href="index.php">HOME</a></li>
                <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'services.php') ? 'active' : ''; ?>"><a class="nav-link" href="services.php">ABOUT US</a></li>
                <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'departments.php') ? 'active' : ''; ?>"><a class="nav-link" href="departments.php">DEPARTMENTS</a></li>
                <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'doctors.php') ? 'active' : ''; ?>"><a class="nav-link" href="doctors.php">DOCTORS</a></li>
                <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'patient-guide.php') ? 'active' : ''; ?>"><a class="nav-link" href="patient-guide.php">PATIENT GUIDE</a></li>
                <li class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'contact-us.php') ? 'active' : ''; ?>"><a class="nav-link" href="contact-us.php">CONTACT</a></li>
                <li class="nav-item ml-lg-3">
                    <a class="btn btn-primary-custom px-4 py-2 text-white font-weight-bold rounded-pill" href="index.php#auth-section">LOGIN</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
