<?php
require_once __DIR__ . '/bootstrap/init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Document Management System</title>
    <link rel="stylesheet" href="<?= asset_url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset_url('css/bootstrap.css') ?>">
</head>
<body class="skin-admin">
    <div class="landing-page">
    
    <header class="landing-header">
    <div class="landing-brand" style="display: flex; align-items: center; gap: 10px;">
        <a href="javascript:location.reload()" style="text-decoration: none; display: flex; align-items: center;" title="Refresh">
            <img src="<?= asset_url('logo/default.png') ?>" alt="Logo" class="filestac-icon" style="width: 32px; height: 32px; object-fit: contain; display: block;">
        </a>
        <span style="font-weight: bold; font-size: 18px; letter-spacing: 0.5px;">FILESTAC DMS</span>
    </div>
    <nav class="landing-nav">
        <a href="#home" class="nav-link active">Home</a>
        <a href="#about" class="nav-link">About</a>
        <a href="#why-us" class="nav-link">Why Us</a>
        <!-- FIX: Added 'nav-link' here so your JavaScript can read it cleanly -->
        <a href="<?= page_url('contact_us_form.php') ?>" class="nav-link btn btn-outline login-btn">Contact Us Form</a>
    </nav>
</header>


    <main class="landing-main">
        <!-- FIX: Grouped the entire hero presentation cleanly inside the #home tracking box -->
        <section id="home" class="landing-hero">
            <div class="hero-left-content">
                <p class="landing-eyebrow">Secure • Simple • Organized</p>
                <h1>Manage your documents with confidence.</h1>
                <p class="landing-text">FILESTAC DMS helps your team store, share, and track documents in one clean workspace.</p>
                <div class="landing-actions">
                    <a href="<?= page_url('login.php') ?>" class="btn btn-primary">Go to Login</a>
                    <a href="#about" class="btn btn-outline">Learn More</a>
                </div>
            </div>
            <div class="landing-card">
                <h3>What you can do</h3>
                <ul>
                    <li>Upload and organize documents</li>
                    <li>Share files securely with others</li>
                    <li>Track activity and version history</li>
                </ul>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="landing-section">
            <h2>About FILESTAC DMS</h2>
            <p>FILESTAC DMS is a lightweight document management system built for teams that want a practical way to manage files without unnecessary complexity.</p>
        </section>

        <!-- Why Us Section -->
        <section id="why-us" class="landing-section">
            <h2>Why FILESTAC DMS?</h2>
            <div class="grid-container">
                <div class="card">
                    <h3>Simple Workflow</h3>
                    <p>Keep document handling clear with a user-friendly interface and focused actions.</p>
                </div>
                <div class="card">
                    <h3>Role-Based Access</h3>
                    <p>Admins, contributors, and casual users each get the access they need.</p>
                </div>
                <div class="card">
                    <h3>Built-In Security</h3>
                    <p>Secure login, protected uploads, and audit tracking help keep your content organized.</p>
                </div>
            </div>
        </section>

        <!-- Call to Action Section -->
        <section class="landing-section landing-cta">
            <h2>Ready to get started?</h2>
            <a href="<?= page_url('login.php') ?>" class="btn btn-primary">Login to your workspace</a>
        </section>
    </main>

    <footer class="landing-footer">
        <div class="footer-top">
            <div class="footer-brand-side">
                <div class="landing-brand">
                    <span class="filestac-icon">&#x1F95D;</span>
                    <span>FILESTAC DMS</span>
                </div>
                <p class="footer-tagline">Organizing files cleanly, securing work simply.</p>
            </div>
            <div class="footer-links-side">
                <div class="footer-col">
                    <h4>Navigation</h4>
                    <a href="#">Back to Top</a>
                    <a href="#about">About System</a>
                    <a href="#why-us">Why Us</a>
                </div>
                <div class="footer-col">
                    <h4>Access</h4>
                    <a href="<?= page_url('login.php') ?>">Workspace Login</a>
                    <span class="footer-note">Registration open for Admin & Contributor only</span>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 FILESTAC DMS. All rights reserved.</p>
            <div class="footer-status">
                <span class="status-dot"></span> Allen Gabriel S. Gaspar
            </div>
        </div>
    </footer>

    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const navLinks = document.querySelectorAll(".landing-nav .nav-link:not(.login-btn)");
        const sections = document.querySelectorAll("section[id]");
        let isClickScrolling = false;

        // 1. CLICK STATE CODES
        navLinks.forEach(link => {
            link.addEventListener("click", function () {
                isClickScrolling = true;
                navLinks.forEach(item => item.classList.remove("active"));
                this.classList.add("active");

                setTimeout(() => {
                    isClickScrolling = false;
                }, 800);
            });
        });

        // 2. SCROLL STATE CODES
        window.addEventListener("scroll", function () {
            if (isClickScrolling) return;

            let currentSectionId = "home";
            const scrollPosition = window.scrollY + 200; // Offset spacing parameter padding

            sections.forEach(section => {
                if (scrollPosition >= section.offsetTop) {
                    currentSectionId = section.getAttribute("id");
                }
            });

            navLinks.forEach(link => {
                link.classList.remove("active");
                if (link.getAttribute("href") === `#${currentSectionId}`) {
                    link.classList.add("active");
                }
            });
        });
    });
    </script>
</body>
</html>
