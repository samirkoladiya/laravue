<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer id="footer" class="footer">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
          <Link href="/" class="d-flex align-items-center">
            <span class="sitename">Lara Vue</span>
          </Link>
          <div class="footer-contact pt-3">
            <p>A108 Adam Street</p>
            <p>New York, NY 535022</p>
            <p class="mt-3"><strong>Phone:</strong> <span>+1 5589 55488 55</span></p>
            <p><strong>Email:</strong> <span>info@example.com</span></p>
          </div>
        </div>

        <div class="col-lg-4 col-md-3 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><i class="bi bi-chevron-right"></i> <Link href="/">Home</Link></li>
            <li><i class="bi bi-chevron-right"></i> <Link href="/about">About</Link></li>
            <li><i class="bi bi-chevron-right"></i> <Link href="/services">Services</Link></li>
            <li><i class="bi bi-chevron-right"></i> <Link href="/team">Team</Link></li>
            <li><i class="bi bi-chevron-right"></i> <Link href="/contact">Contact</Link></li>
          </ul>
        </div>

        <div class="col-lg-4 col-md-3 footer-links">
          <h4>Our Services</h4>
          <ul>
            <li><i class="bi bi-chevron-right"></i> <Link href="/services">Web Development</Link></li>
            <li><i class="bi bi-chevron-right"></i> <Link href="/services">UI/UX Design</Link></li>
            <li><i class="bi bi-chevron-right"></i> <Link href="/services">Digital Strategy</Link></li>
            <li><i class="bi bi-chevron-right"></i> <Link href="/services">Ongoing Support</Link></li>
          </ul>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">Lara Vue</strong> <span>All Rights Reserved</span></p>
      <div class="credits">
        <!-- All the links in the footer should remain intact. -->
        <!-- You can delete the links only if you've purchased the pro version. -->
        <!-- Licensing information: https://bootstrapmade.com/license/ -->
        <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
        Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a> | <a href="https://bootstrapmade.com/tools/">DevTools</a>
      </div>
    </div>

  </footer>
</template>