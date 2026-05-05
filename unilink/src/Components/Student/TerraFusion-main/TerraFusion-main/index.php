<?php
session_start();
require_once 'config.php';
// Initialize menu categories array for Mahmoud AI
$menuCategories = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT meal_type FROM meals ORDER BY meal_type");
    $mealTypes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($mealTypes as $type) {
        $stmt = $pdo->prepare("SELECT meal_id, meal_name, description, price, image, meal_type FROM meals WHERE meal_type = :meal_type");
        $stmt->execute([':meal_type' => $type]);
        $meals = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($meals)) {
            $menuCategories[$type] = $meals;
        }
    }
} catch(Exception $e) {
    error_log("Error fetching menu for chatbot: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Terra Fusion</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

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
  
  <!-- Removed scripts from head - moved to bottom of body -->
  
  <!-- Icon Fonts -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>

</head>

<body class="index-page <?php echo (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) ? 'logged-in' : ''; ?>">
  <?php 
  require_once 'cart_functions.php';

  $cart_count = 0;
  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user_id'])) {
      $cart_id = getOrCreateCart($_SESSION['user_id']);
      
      if ($cart_id === false) {
          // User exists in session but not in DB (orphaned session)
          session_unset();
          session_destroy();
          header("Location: index.php");
          exit;
      }
      
      $cart_count = getCartCount($cart_id);
  }
  ?>


  <header id="header" class="header fixed-top">

    <div class="topbar d-flex align-items-center">
      <div class="container d-flex justify-content-center justify-content-md-between">

      </div>
    </div><!-- End Top Bar -->

    <div class="branding d-flex align-items-cente">

      <div class="container position-relative d-flex align-items-center justify-content-between">
        <a href="index.php" class="logo d-flex align-items-center me-auto me-xl-0">
          <!-- Uncomment the line below if you also wish to use an image logo -->
          <!-- <img src="assets/img/logo.png" alt=""> -->
          <h1 class="sitename">Terra Fusion</h1>
        </a>

        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="#hero" class="active">Home</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#specials">Specials</a></li>
            <li><a href="#events">Events</a></li>
            <li><a href="#contact">Contact</a></li>
          </ul>
          <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
          <div class="d-flex">
            <a class="btn-book-a-table d-none d-xl-block" href="userprofile.php" title="My Profile"><i class="bi bi-person-circle"></i></a>
            <a class="btn-book-a-table d-none d-xl-block position-relative" href="cart.php" title="Cart">
              <i class="bi bi-cart3"></i>
              <span id="cart-badge" class="position-absolute translate-middle badge d-flex align-items-center justify-content-center" style="<?php echo $cart_count > 0 ? '' : 'display: none;'; ?>"><?php echo $cart_count; ?></span>
            </a>
            <a class="btn-book-a-table d-none d-xl-block" href="logout.php" title="Logout"><i class="bi bi-box-arrow-right"></i></a>

          </div>
        <?php else: ?>
          <a class="btn-book-a-table d-none d-xl-block" href="userprofile.php" title="Profile"><i class="bi bi-person-circle"></i></a>
        <?php endif; ?>

      </div>

    </div>

  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background">

      <div class="swiper init-swiper" data-aos="fade-in">
        <script type="application/json" class="swiper-config">
          {
            "loop": true,
            "speed": 1000,
            "autoplay": {
              "delay": 5000,
              "disableOnInteraction": false
            },
            "effect": "fade",
            "fadeEffect": {
              "crossFade": true
            },
            "allowTouchMove": false
          }
        </script>
        <div class="swiper-wrapper">
          <div class="swiper-slide"><img src="images/hero-slide-1.jpg" alt=""></div>
          <div class="swiper-slide"><img src="images/hero-slide-2.jpg" alt=""></div>
          <div class="swiper-slide"><img src="images/hero-slide-3.jpg" alt=""></div>
          <div class="swiper-slide"><img src="images/hero-slide-4.jpg" alt=""></div>
          <div class="swiper-slide"><img src="images/hero-slide-5.jpg" alt=""></div>
          <div class="swiper-slide"><img src="images/hero-slide-6.jpg" alt=""></div>
          <div class="swiper-slide"><img src="images/hero-slide-7.jpg" alt=""></div>
        </div>
      </div>

      <div class="container d-flex flex-column justify-content-center align-items-center text-center position-relative" data-aos="fade-up" data-aos-delay="100">
        <div class="row w-100 justify-content-center">
          <div class="col-lg-8 d-flex flex-column align-items-center align-items-lg-start">
            <h2 data-aos="fade-up" data-aos-delay="100">Welcome to <span>Terra Fusion</span></h2>
            <p data-aos="fade-up" data-aos-delay="200">Experience flavor reimagined.</p>
            <div class="d-flex mt-4" data-aos="fade-up" data-aos-delay="300">
              <a href="menu.php" class="cta-btn">Our Menu</a>
              <a href="#book-a-table" class="cta-btn">Book a Table</a>
            </div>
          </div>
        </div>
      </div>

    </section><!-- /Hero Section -->

    <!-- About Section -->
    <section id="about" class="about section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">
          <div class="col-lg-6 order-1 order-lg-2">
            <img src="images/about-small.jpg" class="img-fluid about-img" alt="">
          </div>
          <div class="col-lg-6 order-2 order-lg-1 content">
            <h3>Welcome to Terra Fusion</h3>
            <p class="fst-italic">
                Where culinary excellence meets innovative technology for a seamless dining experience.
            </p>
            <ul>
                <li><i class="bi bi-check2-all"></i> <span>Experience our digital-first approach to dining with our state-of-the-art ordering system</span></li>
                <li><i class="bi bi-check2-all"></i> <span>Enjoy effortless ordering through our user-friendly interface, available for both in-house and online customers</span></li>
                <li><i class="bi bi-check2-all"></i> <span>Track your order in real-time from kitchen to table with our advanced order management system</span></li>
            </ul>
            <p>
                At Terra Fusion, we've revolutionized the dining experience by integrating cutting-edge technology with exceptional cuisine. Our digital platform ensures your orders are accurate, your service is prompt, and your dining experience is nothing short of extraordinary. Whether you're joining us for a relaxed meal or ordering for delivery, our system is designed to make every aspect of your dining experience smooth and enjoyable.
            </p>
        </div>
        </div>

      </div>

    </section><!-- /About Section -->

    <!-- Why Us Section -->
    <section id="why-us" class="why-us section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>WHY US</h2>
        <p>Why Choose Our Restaurant</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card-item">
              <span>01</span>
              <h4><a href="" class="stretched-link">Seamless Digital Experience</a></h4>
              <p>Our state-of-the-art ordering system makes browsing, ordering, and paying effortless. Enjoy a smooth digital experience from start to finish.</p>
            </div>
          </div><!-- Card Item -->

          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card-item">
              <span>02</span>
              <h4><a href="" class="stretched-link">Real-Time Order Tracking</a></h4>
              <p>Know exactly where your order is at all times. From kitchen preparation to table service, track your meal's journey in real-time.</p>
            </div>
          </div><!-- Card Item -->

          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
            <div class="card-item">
              <span>03</span>
              <h4><a href="" class="stretched-link">Personalized Service</a></h4>
              <p>Our system remembers your preferences and order history, allowing us to provide a personalized dining experience every time you visit.</p>
            </div>
          </div><!-- Card Item -->

        </div>

      </div>

    </section><!-- /Why Us Section -->

    <!-- Specials Section -->
    <section id="specials" class="specials section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Specials</h2>
        <p>Check Our Specials</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row">
          <div class="col-lg-3">
            <ul class="nav nav-tabs flex-column">
              <li class="nav-item">
                <a class="nav-link active show" data-bs-toggle="tab" href="#specials-tab-1">Chef's Signature</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#specials-tab-2">Ocean's Catch</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#specials-tab-3">Appetizers</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#specials-tab-4">Comfort Classics</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#specials-tab-5">Sweet Finale</a>
              </li>
            </ul>
          </div>
          <div class="col-lg-9 mt-4 mt-lg-0">
            <div class="tab-content">
              <div class="tab-pane active show" id="specials-tab-1">
                <div class="row">
                  <div class="col-lg-8 details order-2 order-lg-1">
                    <h3>Truffle Pasta</h3>
                    <p class="fst-italic">Fettuccine pasta in a creamy sauce infused with aromatic truffle oil and topped with shaved black truffles.</p>
                    <p>Indulge in the earthy elegance of our Truffle Pasta. We use only the finest Italian fettuccine, coated in a velvety cream sauce that perfectly balances the intense aroma of black truffles. A true signature dish for the discerning palate.</p>
                  </div>
                  <div class="col-lg-4 text-center order-1 order-lg-2">
                    <img src="images/meals-imgs/Truffle Pasta.jpg" alt="Truffle Pasta" class="img-fluid">
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="specials-tab-2">
                <div class="row">
                  <div class="col-lg-8 details order-2 order-lg-1">
                    <h3>Grilled Salmon Delight</h3>
                    <p class="fst-italic">Perfectly grilled salmon fillet seasoned with herbs and served with asparagus and lemon butter sauce.</p>
                    <p>Our Atlantic salmon is sourced fresh and grilled to achieve a flaky texture and crispy skin. Complemented by the brightness of our house-made lemon butter sauce and fresh asparagus, it's a healthy yet indulgent choice.</p>
                  </div>
                  <div class="col-lg-4 text-center order-1 order-lg-2">
                    <img src="images/meals-imgs/Grilled Salmon Delight.jpg" alt="Grilled Salmon Delight" class="img-fluid">
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="specials-tab-3">
                <div class="row">
                  <div class="col-lg-8 details order-2 order-lg-1">
                    <h3>Bruschetta Trio</h3>
                    <p class="fst-italic">Three varieties of fresh bruschetta with tomatoes, basil, and balsamic glaze.</p>
                    <p>Start your meal with a burst of freshness. Our trio features classic tomato and basil, roasted pepper with goat cheese, and a rich olive tapenade. All served on toasted artisan bread drizzled with aged balsamic.</p>
                  </div>
                  <div class="col-lg-4 text-center order-1 order-lg-2">
                    <img src="images/meals-imgs/Bruschetta Trio.jpg" alt="Bruschetta Trio" class="img-fluid">
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="specials-tab-4">
                <div class="row">
                  <div class="col-lg-8 details order-2 order-lg-1">
                    <h3>Chicken Alfredo Pasta</h3>
                    <p class="fst-italic">Grilled chicken breast tossed in a rich, creamy Alfredo sauce with fettuccine pasta.</p>
                    <p>Comfort food at its finest. Tender slices of grilled chicken breast are tossed with perfectly cooked pasta in our signature Alfredo sauce, made with heavy cream, butter, and plenty of Parmesan cheese.</p>
                  </div>
                  <div class="col-lg-4 text-center order-1 order-lg-2">
                    <img src="images/meals-imgs/Chicken Alfredo Pasta.jpg" alt="Chicken Alfredo Pasta" class="img-fluid">
                  </div>
                </div>
              </div>
              <div class="tab-pane" id="specials-tab-5">
                <div class="row">
                  <div class="col-lg-8 details order-2 order-lg-1">
                    <h3>Tiramisu</h3>
                    <p class="fst-italic">Classic Italian dessert made with layers of coffee-soaked ladyfingers and mascarpone cream.</p>
                    <p>End on a sweet note with our authentic Tiramisu. We use premium espresso to soak the ladyfingers, ensuring every bite is moist and flavorful. Layered with a light, airy mascarpone filling and dusted with cocoa powder.</p>
                  </div>
                  <div class="col-lg-4 text-center order-1 order-lg-2">
                    <img src="images/meals-imgs/Tiramisu.jpg" alt="Tiramisu" class="img-fluid">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

    </section><!-- /Specials Section -->

    <!-- Events Section -->
    <section id="events" class="events section">

      <img class="slider-bg" src="assets/img/events-bg.jpg" alt="" data-aos="fade-in">

      <div class="container">

        <div class="swiper init-swiper" data-aos="fade-up" data-aos-delay="100">
          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              }
            }
          </script>
          <div class="swiper-wrapper">

            <div class="swiper-slide">
              <div class="row gy-4 event-item">
                <div class="col-lg-6">
                  <img src="images/birthday party.avif" class="img-fluid" alt="">
                </div>
                <div class="col-lg-6 pt-4 pt-lg-0 content">
                  <h3>Birthday Parties</h3>
                  <div class="price">
                    <p><span>189EGP</span></p>
                  </div>
                  <p class="fst-italic">
                    Celebrate your special moments with us. Our dedicated team ensures a fun and memorable experience with custom menus and decorations.
                  </p>
                  <ul>
                    <li><i class="bi bi-check2-circle"></i> <span>Customizable cake and dessert options</span></li>
                    <li><i class="bi bi-check2-circle"></i> <span>Private area reservation available</span></li>
                    <li><i class="bi bi-check2-circle"></i> <span>Dedicated host for your event</span></li>
                  </ul>
                  <p>
                    Make your birthday unforgettable at Terra Fusion. We handle all the details so you can focus on celebrating with your loved ones.
                  </p>
                </div>
              </div>
            </div><!-- End Slider item -->

            <div class="swiper-slide">
              <div class="row gy-4 event-item">
                <div class="col-lg-6">
                  <img src="images/private party.png" class="img-fluid" alt="">
                </div>
                <div class="col-lg-6 pt-4 pt-lg-0 content">
                  <h3>Private Parties</h3>
                  <div class="price">
                    <p><span>290EGP</span></p>
                  </div>
                  <p class="fst-italic">
                    Host your exclusive events in our private dining area. Perfect for corporate meetings or intimate family gatherings.
                  </p>
                  <ul>
                    <li><i class="bi bi-check2-circle"></i> <span>Secluded space for privacy</span></li>
                    <li><i class="bi bi-check2-circle"></i> <span>Tailored menus and premium wine pairing</span></li>
                    <li><i class="bi bi-check2-circle"></i> <span>Audio-visual equipment support</span></li>
                  </ul>
                  <p>
                     Enjoy an exclusive atmosphere with our private party packages. We offer top-tier service to ensure your meaningful conversations are uninterrupted.
                  </p>
                </div>
              </div>
            </div><!-- End Slider item -->

            <div class="swiper-slide">
              <div class="row gy-4 event-item">
                <div class="col-lg-6">
                  <img src="images/custom party.avif" class="img-fluid" alt="">
                </div>
                <div class="col-lg-6 pt-4 pt-lg-0 content">
                  <h3>Custom Parties</h3>
                  <div class="price">
                    <p><span>99EGP</span></p>
                  </div>
                  <p class="fst-italic">
                    Let us bring your vision to life. Whether it's a themed party or a grand celebration, we tailor every detail to your needs.
                  </p>
                  <ul>
                    <li><i class="bi bi-check2-circle"></i> <span>Flexible seating arrangements</span></li>
                    <li><i class="bi bi-check2-circle"></i> <span>Themed decorations and lighting</span></li>
                    <li><i class="bi bi-check2-circle"></i> <span>Personalized menu consultation</span></li>
                  </ul>
                  <p>
                    No idea is too big or too small. Our event planners work closely with you to design a custom party that reflects your style and personality.
                  </p>
                </div>
              </div>
            </div><!-- End Slider item -->

          </div>
          <div class="swiper-pagination"></div>
        </div>

      </div>

    </section><!-- /Events Section -->

    <!-- Book A Table Section -->
    <section id="book-a-table" class="book-a-table section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>RESERVATION</h2>
        <p>Book a Table</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <form action="forms/book-a-table.php" method="post" role="form" class="php-email-form">
          <div class="row gy-4">
            <div class="col-lg-4 col-md-6">
              <input type="text" name="name" class="form-control" id="name" placeholder="Your Name" required="">
            </div>
            <div class="col-lg-4 col-md-6">
              <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required="">
            </div>
            <div class="col-lg-4 col-md-6">
              <input type="text" class="form-control" name="phone" id="phone" placeholder="Your Phone" required="">
            </div>
            <div class="col-lg-4 col-md-6">
              <input type="date" name="date" class="form-control" id="date" placeholder="Date" required="">
            </div>
            <div class="col-lg-4 col-md-6">
              <input type="time" class="form-control" name="time" id="time" placeholder="Time" required="">
            </div>
            <div class="col-lg-4 col-md-6">
              <input type="number" class="form-control" name="people" id="people" placeholder="# of people" required="">
            </div>
          </div>

          <div class="form-group mt-3">
            <textarea class="form-control" name="message" rows="5" placeholder="Message"></textarea>
          </div>

          <div class="text-center mt-3">
            <div class="loading">Loading</div>
            <div class="error-message"></div>
            <div class="sent-message">Your booking request was sent. We will call back to confirm reservations. Thank you!</div>
            <button type="submit">Book a Table</button>
          </div>
        </form><!-- End Reservation Form -->

      </div>

    </section><!-- /Book A Table Section -->

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Testimonials</h2>
        <p>What they're saying about us</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="swiper init-swiper" data-speed="600" data-delay="5000" data-breakpoints="{ &quot;320&quot;: { &quot;slidesPerView&quot;: 1, &quot;spaceBetween&quot;: 40 }, &quot;1200&quot;: { &quot;slidesPerView&quot;: 3, &quot;spaceBetween&quot;: 40 } }">
          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              },
              "breakpoints": {
                "320": {
                  "slidesPerView": 1,
                  "spaceBetween": 40
                },
                "1200": {
                  "slidesPerView": 3,
                  "spaceBetween": 20
                }
              }
            }
          </script>
          <div class="swiper-wrapper">

            <div class="swiper-slide">
              <div class="testimonial-item">
            <p>
                <i class="bi bi-quote quote-icon-left"></i>
                <span>"Terra Fusion is a revelation! The combination of flavors is unlike anything I've tasted before. The Ribeye is a must-try. Definitely coming back!"</span>
                <i class="bi bi-quote quote-icon-right"></i>
                </p>
                <img src="assets/img/testimonials/testimonials-1.jpg" class="testimonial-img" alt="">
                <h3>James Dalton</h3>
                <h4>Food Critic</h4>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>"The atmosphere is perfect for a romantic evening. The staff was incredibly attentive, and the dessert was Divine. Truly a five-star experience."</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
                <img src="assets/img/testimonials/testimonials-2.jpg" class="testimonial-img" alt="">
                <h3>Sara Wilson</h3>
                <h4>Artist</h4>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>"I hosted my birthday party here and it was flawless. The private room was beautiful, and the custom menu was a hit with all my guests."</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
                <img src="assets/img/testimonials/testimonials-3.jpg" class="testimonial-img" alt="">
                <h3>Jena Karlis</h3>
                <h4>Store Owner</h4>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>"As a vegan, I often struggle to find good options, but the Spicy Miso Ramen here is incredible. It looks and tastes amazing. Highly appreciate the variety!"</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
                <img src="assets/img/testimonials/testimonials-4.jpg" class="testimonial-img" alt="">
                <h3>Matt Brandon</h3>
                <h4>Freelancer</h4>
              </div>
            </div><!-- End testimonial item -->

            <div class="swiper-slide">
              <div class="testimonial-item">
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>"Fast service, great food, and very modern ordering system. I love how I can track my order. Terra Fusion is leading the way in dining tech!"</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
                <img src="assets/img/testimonials/testimonials-5.jpg" class="testimonial-img" alt="">
                <h3>John Larson</h3>
                <h4>Entrepreneur</h4>
              </div>
            </div><!-- End testimonial item -->

          </div>
          <div class="swiper-pagination"></div>
        </div>

      </div>

    </section><!-- /Testimonials Section -->

    <!-- Contact Section -->
    <section id="contact" class="contact section" style="position: relative; overflow: hidden;">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Contact</h2>
        <p>Contact Us</p>
      </div><!-- End Section Title -->

      <div class="mb-5" data-aos="fade-up" data-aos-delay="200">
        <iframe style="border:0; width: 100%; height: 400px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3461.165763775662!2d31.49196591566666!3d30.16846551566666!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14581bab30f3291d%3A0x1b138aefe2d8bedb!2zMVkgSW5zdHJpY3Rpb24gVW5pdGl2YXRpb24gKE1JVSk!5e0!3m2!1sen!2sus!4v1756120902456!5m2!1sen!2sus" frameborder="0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div><!-- End Google Maps -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4 justify-content-center align-items-center">

          <div class="col-lg-5">
            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
              <i class="bi bi-geo-alt flex-shrink-0"></i>
              <div>
                <h3>Location</h3>
                <p>Egypt, Cairo, Obour City, MIU</p>
              </div>
            </div><!-- End Info Item -->

            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
              <i class="bi bi-telephone flex-shrink-0"></i>
              <div>
                <h3>Open Hours</h3>
                <p>Monday-Sunday:<br>11:00 AM - 11:00 PM</p>
              </div>
            </div><!-- End Info Item -->

            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
              <i class="bi bi-telephone flex-shrink-0"></i>
              <div>
                <h3>Call Us</h3>
                <p>+20 101 234 5678</p>
              </div>
            </div><!-- End Info Item -->

          </div>

          <div class="col-lg-3 d-flex justify-content-center">
            <a href="meet-us.php" class="meet-us-btn">Meet Us</a>
          </div>

        </div>

      </div>

    </section><!-- /Contact Section -->

  </main>

  <footer id="footer" class="footer" style="position: relative; overflow: visible;">
    <!-- Floating Shapes Background -->
    <div class="floating-shapes">
      <span class="shape" style="--i:11; --x: 05%; --d: 0s;">✦</span>
      <span class="shape" style="--i:12; --x: 15%; --d: 2s;">✧</span>
      <span class="shape" style="--i:15; --x: 25%; --d: 4s;">●</span>
      <span class="shape" style="--i:13; --x: 35%; --d: 1s;">◆</span>
      <span class="shape" style="--i:18; --x: 45%; --d: 5s;">✨</span>
      <span class="shape" style="--i:14; --x: 55%; --d: 3s;">✦</span>
      <span class="shape" style="--i:16; --x: 65%; --d: 6s;">✧</span>
      <span class="shape" style="--i:19; --x: 75%; --d: 2s;">●</span>
      <span class="shape" style="--i:20; --x: 85%; --d: 4s;">◆</span>
      
      <span class="shape" style="--i:21; --x: 10%; --d: 1.5s;">✨</span>
      <span class="shape" style="--i:22; --x: 20%; --d: 3.5s;">✦</span>
      <span class="shape" style="--i:23; --x: 30%; --d: 5.5s;">✧</span>
      <span class="shape" style="--i:24; --x: 40%; --d: 2.5s;">●</span>
      <span class="shape" style="--i:25; --x: 50%; --d: 4.5s;">◆</span>
      <span class="shape" style="--i:26; --x: 60%; --d: 0.5s;">✨</span>
      <span class="shape" style="--i:27; --x: 70%; --d: 6.5s;">✦</span>
      <span class="shape" style="--i:28; --x: 80%; --d: 1.2s;">✧</span>
      <span class="shape" style="--i:29; --x: 90%; --d: 3.2s;">●</span>
      <span class="shape" style="--i:30; --x: 02%; --d: 5.2s;">◆</span>
      <span class="shape" style="--i:31; --x: 12%; --d: 2.8s;">✨</span>
      <span class="shape" style="--i:32; --x: 22%; --d: 4.8s;">✦</span>
      <span class="shape" style="--i:33; --x: 32%; --d: 0.8s;">✧</span>
      <span class="shape" style="--i:34; --x: 42%; --d: 6.8s;">●</span>
      <span class="shape" style="--i:35; --x: 52%; --d: 1.8s;">◆</span>
      <span class="shape" style="--i:36; --x: 62%; --d: 3.8s;">✨</span>
      <span class="shape" style="--i:37; --x: 72%; --d: 5.8s;">✦</span>
    </div>

    <div class="container footer-top">
      <div class="row gy-4 justify-content-center text-center">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.php" class="logo d-flex align-items-center">
            <span class="sitename">Terra Fusion</span>
          </a>
          <div class="footer-contact pt-3">
            <p>Misr International University</p>
            <p>Egypt, Cairo, Obour City, MIU</p>
            <p class="mt-3"><strong>Phone:</strong> <span>+20 101 234 5678</span></p>
            <p><strong>Email:</strong> <span>contact@terrafusion.com</span></p>
          </div>
          <div class="social-links d-flex mt-4 justify-content-center">
            <a href=""><i class="bi bi-twitter-x"></i></a>
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
            <a href=""><i class="bi bi-linkedin"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="#about">About us</a></li>
            <li><a href="menu.php">Menu</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="meet-us.php">Meet Us</a></li>
          </ul>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">Terra Fusion</strong> <span>All Rights Reserved</span></p>
      <div class="credits">
      </div>
    </div>

  </footer>

  <!-- Signup/Login Modal -->
  <div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="signupModalLabel">Welcome to Terra Fusion</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ul class="nav nav-tabs mb-3" id="authTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-tab-pane" type="button" role="tab" aria-controls="login-tab-pane" aria-selected="true">Login</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register-tab-pane" type="button" role="tab" aria-controls="register-tab-pane" aria-selected="false">Register</button>
            </li>
          </ul>
          <div class="tab-content" id="authTabContent">
            <!-- Login Tab -->
            <div class="tab-pane fade show active" id="login-tab-pane" role="tabpanel" aria-labelledby="login-tab" tabindex="0">
              <form id="loginForm" method="POST" action="auth.php">
                <input type="hidden" name="action" value="login">
                <div class="mb-3">
                  <label for="loginEmail" class="form-label">Email address</label>
                  <input type="email" class="form-control" id="loginEmail" name="email" required>
                </div>
                <div class="mb-3">
                  <label for="loginPassword" class="form-label">Password</label>
                  <input type="password" class="form-control" id="loginPassword" name="password" required>
                </div>
                <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-primary">Login</button>
                </div>
              </form>
            </div>
            <!-- Register Tab -->
            <div class="tab-pane fade" id="register-tab-pane" role="tabpanel" aria-labelledby="register-tab" tabindex="0">
              <form id="registerForm" method="POST" action="auth.php">
                <input type="hidden" name="action" value="register">
                <div class="mb-3">
                  <label for="regUsername" class="form-label">Username</label>
                  <input type="text" class="form-control" id="regUsername" name="username" required>
                </div>
                <div class="mb-3">
                  <label for="regEmail" class="form-label">Email address</label>
                  <input type="email" class="form-control" id="regEmail" name="email" required>
                </div>
                <div class="mb-3">
                  <label for="regPhone" class="form-label">Phone Number</label>
                  <input type="tel" class="form-control" id="regPhone" name="phone" required>
                </div>
                <div class="mb-3">
                  <label for="regPassword" class="form-label">Password</label>
                  <input type="password" class="form-control" id="regPassword" name="password" required minlength="6">
                </div>
                <div class="mb-3">
                  <label for="regConfirmPassword" class="form-label">Confirm Password</label>
                  <input type="password" class="form-control" id="regConfirmPassword" name="confirm_password" required>
                </div>
                <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-primary">Create Account</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>


  <!-- Main JS File -->
  <script src="main.js"></script>

  <!-- Vendor JS Files -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/isotope-layout@3.0.6/dist/isotope.pkgd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/swiper@8.4.7/swiper-bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/imagesloaded@5.0.0/imagesloaded.pkgd.min.js"></script>
  
  <!-- Main JS File -->
  <script src="main.js"></script>
  
  <!-- Load cart.js first, then Mahmoud -->
  <script src="assets/js/cart.js"></script>
  <script>
      window.terraMenu = <?php echo json_encode($menuCategories); ?>;
  </script>
  <script src="assets/js/chef-mahmoud.js"></script>
  
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Main JS File -->
  <script src="main.js"></script>
  <script src="assets/js/reservation.js"></script>
</body>
</html>
