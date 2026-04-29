<!-- Footer -->
<footer class="public-footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-5">
                <a class="footer-brand" href="#"><i class="fa fa-heartbeat mr-2"></i> GLOBAL HOSPITALS</a>
                <p>Compassionate multispecialty care with modern diagnostics, trusted clinicians, and patient-first service.</p>
            </div>
            <div class="col-lg-2 col-md-4 mb-5">
                <h5 class="footer-heading">Explore</h5>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="services.php">About Us</a></li>
                    <li><a href="doctors.php">Doctors</a></li>
                    <li><a href="contact-us.php">Contact</a></li>
                    <li><a href="staff-login.php" class="text-white-50"><i class="fa fa-lock mr-1"></i>Staff Portal</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-4 mb-5 ml-auto">
                <h5 class="footer-heading">Reach Us</h5>
                <ul class="footer-links">
                    <li><i class="fa fa-phone mr-2"></i> +91 674 2725 123</li>
                    <li><i class="fa fa-envelope-o mr-2"></i> care@globalhospitals.in</li>
                    <li><i class="fa fa-map-marker mr-2"></i> Plot No. 12, KIIT Road, Patia, Bhubaneswar, Odisha 751024</li>
                </ul>
            </div>
        </div>
        <hr style="border-color: rgba(255,255,255,0.05);">
        <div class="text-center mt-4">
            <p class="small mb-0">© 2026 Global Hospitals. All rights reserved.</p>
        </div>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
    window.onscroll = function() {
        var nav = document.getElementById('mainNav');
        if (nav && window.pageYOffset > 50) {
            nav.classList.add('scrolled');
        } else if (nav) {
            nav.classList.remove('scrolled');
        }
    };

    // Auto-close mobile menu on link click
    $('.navbar-nav .nav-link, .navbar-nav .btn').on('click', function(){
        $('.navbar-collapse').collapse('hide');
    });
</script>
