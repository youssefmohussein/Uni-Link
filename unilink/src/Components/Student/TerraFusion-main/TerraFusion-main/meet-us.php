<?php
// Start session and include necessary files
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Meet Our Team - Terra Fusion</title>
  <meta name="description" content="Meet the talented team behind Terra Fusion">
  <meta name="keywords" content="team, about us, developers, designers, Terra Fusion">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/swiper@8.4.7/swiper-bundle.min.css" rel="stylesheet">
  
  <!-- Main CSS File -->
  <link href="main.css" rel="stylesheet">
  
  <!-- Vendor JS Files -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/isotope-layout@3.0.6/dist/isotope.pkgd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@8.4.7/swiper-bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/imagesloaded@5.0.0/imagesloaded.pkgd.min.js"></script>
  
  <!-- Icon Fonts -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #0c0b09;
            color: #fff;
        }
        .team-section {
            padding: 150px 0 30px;
            min-height: auto;
        }
        
        .team-container {
            display: flex;
            flex-wrap: nowrap;
            justify-content: space-between;
            gap: 15px;
            overflow-x: auto;
            padding: 5px 0;
            scrollbar-width: none; /* For Firefox */
        }
        
        .team-container::-webkit-scrollbar {
            display: none; /* For Chrome, Safari and Opera */
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #cda45e;
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
            border-bottom: none;
        }
        
        .team-card-wrapper {
            flex: 0 0 18%;
            min-width: 200px;
        }
        
        .team-card {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #cda45e;
            height: 100%;
            display: flex;
            flex-direction: column;
            margin: 0;
        }
        
        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        }
        
        .team-img {
            width: 120px;
            height: 120px;
            margin: 15px auto 0;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid #cda45e;
        }
        
        .team-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .team-card:hover .team-img img {
            transform: scale(1.05);
        }
        
        .team-info {
            padding: 15px;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .team-info h3 {
            font-size: 1rem;
            font-weight: 600;
            margin: 10px 5px 5px;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .team-info p {
            color: #aaa;
            margin: 5px 5px 10px;
            font-size: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .linkedin-btn {
            display: inline-block;
            padding: 5px 12px;
            background: transparent;
            border: 1px solid #cda45e;
            color: #cda45e;
            border-radius: 15px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.75rem;
            transition: all 0.3s ease;
            margin: 10px 5px 15px;
            white-space: nowrap;
        }
        
        .linkedin-btn:hover {
            background: #cda45e;
            color: #000;
        }
        
        .linkedin-btn i {
            margin-right: 5px;
        }
    </style>
</head>
<body>
  <!-- Main JS File -->
  <script src="main.js" defer></script>

  <header id="header" class="header fixed-top">

    <div class="topbar d-flex align-items-center">
      <div class="container d-flex justify-content-center justify-content-md-between">
      </div>
    </div><!-- End Top Bar -->

    <div class="branding d-flex align-items-center">

      <div class="container position-relative d-flex align-items-center justify-content-between">
        <a href="index.php" class="logo d-flex align-items-center me-auto me-xl-0">
          <h1 class="sitename">Terra Fusion</h1>
        </a>

        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="index.php#about">About</a></li>
            <li><a href="index.php#specials">Specials</a></li>
            <li><a href="index.php#events">Events</a></li>
            <li><a href="index.php#contact">Contact</a></li>
          </ul>
          <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

      </div>
        
        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
          <div class="d-flex">
            <a class="btn-book-a-table d-none d-xl-block" href="userprofile.php" title="My Profile"><i class="bi bi-person-circle"></i></a>
            <a class="btn-book-a-table d-none d-xl-block" href="logout.php" title="Logout"><i class="bi bi-box-arrow-right"></i></a>
          </div>
        <?php else: ?>
          <a class="btn-book-a-table d-none d-xl-block" href="userprofile.php" title="Profile"><i class="bi bi-person-circle"></i></a>
        <?php endif; ?>

      </div>

    </div>

  </header>

  <main class="main">
        <section class="team-section">
            <div class="container" data-aos="fade-up">
                <div class="section-title">
                    <h2>Meet Our Team</h2>
                    <p>Passionate individuals creating amazing experiences for you</p>
                </div>

                <div class="team-container">
                    <!-- Team Member 1 -->
                    <div class="team-card-wrapper" data-aos="fade-up" data-aos-delay="100">
                        <div class="team-card">
                            <div class="team-img">
                                <img src="images/salmaahmed.jpg" alt="Team Member 1">
                            </div>
                            <div class="team-info">
                                <h3>Salma Ahmed</h3>
                                <p>Web Developer - Data Scientist</p>
                                <a href="https://www.linkedin.com/in/salma-ahmed-370751315/" class="linkedin-btn" target="_blank">
                                    <i class="bi bi-linkedin"></i> My LinkedIn
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Team Member 2 -->
                    <div class="team-card-wrapper" data-aos="fade-up" data-aos-delay="200">
                        <div class="team-card">
                            <div class="team-img">
                                <img src="images/malakelghamrawy.jpg" alt="Team Member 2">
                            </div>
                            <div class="team-info">
                                <h3>Malak Elghamrawy</h3>
                                <p>Web Developer - Cyber Security</p>
                                <a href="https://www.linkedin.com/in/malak-elghamrawy-395792351/" class="linkedin-btn" target="_blank">
                                    <i class="bi bi-linkedin"></i> My LinkedIn
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Team Member 3 -->
                    <div class="team-card-wrapper" data-aos="fade-up" data-aos-delay="300">
                        <div class="team-card">
                            <div class="team-img">
                                <img src="images/jananasr.jpg" alt="Team Member 3">
                            </div>
                            <div class="team-info">
                                <h3>Jana Nasr</h3>
                                <p>Web Developer - Cyber Security</p>
                                <a href="https://www.linkedin.com/in/jana-nasr-1b7311366/" class="linkedin-btn" target="_blank">
                                    <i class="bi bi-linkedin"></i> My LinkedIn
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Team Member 4 -->
                    <div class="team-card-wrapper" data-aos="fade-up" data-aos-delay="100">
                        <div class="team-card">
                            <div class="team-img">
                                <img src="images/salmahisham.jpg" alt="Team Member 4">
                            </div>
                            <div class="team-info">
                                <h3>Salma Elhefnawi</h3>
                                <p>Web Developer - Data Science</p>
                                <a href="https://www.linkedin.com/in/salma-elhefnawi-602812285/" class="linkedin-btn" target="_blank">
                                    <i class="bi bi-linkedin"></i> My LinkedIn
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Team Member 5 -->
                    <div class="team-card-wrapper" data-aos="fade-up" data-aos-delay="200">
                        <div class="team-card">
                            <div class="team-img">
                                <img src="images/basmaayman.jpg" alt="Team Member 5">
                            </div>
                            <div class="team-info">
                                <h3>Basma Ayman</h3>
                                <p>Web Developer - Cyber Security</p>
                                <a href="https://www.linkedin.com/in/basma-fouda-1441ab366/" class="linkedin-btn" target="_blank">
                                    <i class="bi bi-linkedin"></i> My LinkedIn
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <a href="#" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Template Main JS File -->
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
    <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
    <script src="assets/vendor/php-email-form/validate.js"></script>
    <script src="assets/js/main.js"></script>
    
    <!-- Initialize AOS -->
    <script>
        AOS.init({
            duration: 1000,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });
    </script>

</body>
</html>