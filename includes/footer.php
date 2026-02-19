    <footer class="footer mt-auto">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5 class="footer-brand text-white mb-3"><i class="bi bi-briefcase-fill me-2"></i><?php echo SITE_NAME; ?></h5>
                    <p class="text-white-50 small">Connect with the right job. We help job seekers and employers find each other efficiently.</p>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="text-white mb-3">For Job Seekers</h6>
                    <ul class="list-unstyled small">
                        <li><a href="<?php echo BASE_URL; ?>jobseeker/search_jobs.php" class="footer-link">Browse Jobs</a></li>
                        <li><a href="<?php echo BASE_URL; ?>auth/register.php?role=jobseeker" class="footer-link">Create Account</a></li>
                        <li><a href="<?php echo BASE_URL; ?>about.php" class="footer-link">Career Tips</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="text-white mb-3">For Employers</h6>
                    <ul class="list-unstyled small">
                        <li><a href="<?php echo BASE_URL; ?>employer/post_job.php" class="footer-link">Post a Job</a></li>
                        <li><a href="<?php echo BASE_URL; ?>auth/register.php?role=employer" class="footer-link">Employer Sign Up</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="text-white mb-3">Company</h6>
                    <ul class="list-unstyled small">
                        <li><a href="<?php echo BASE_URL; ?>about.php" class="footer-link">About Us</a></li>
                        <li><a href="<?php echo BASE_URL; ?>contact.php" class="footer-link">Contact</a></li>
                    </ul>
                </div>
            </div>
            <hr class="border-secondary my-4">
            <div class="text-center text-white-50 small">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Final Year Project.</div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
</body>
</html>
