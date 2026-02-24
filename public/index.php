<?php
session_start();
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/db.php';

$userId = $_SESSION['user_id'] ?? 0;
$userName = $_SESSION['name'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Kart</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6fb;
        }

        /* HEADER */
        header {
            background: linear-gradient(90deg, #111, #6366f1);
            color: #fff;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav a {
            color: #fff;
            margin-left: 25px;
            text-decoration: none;
        }

        /* HERO */
        .hero {
            background: linear-gradient(135deg, #667eea, #764ba2, #ff758c);
            padding: 110px 20px;
            color: #fff;
        }

        .hero h2 {
            font-size: 2.8rem;
            font-weight: 700;
        }

        .hero-subtext {
            font-size: 1.3rem;
            margin: 20px 0 30px;
        }

        .btn-custom {
            background: linear-gradient(135deg, #22d3ee, #3b82f6);
            color: #fff;
            padding: 14px 32px;
            border-radius: 50px;
            text-decoration: none;
        }

        .hero-card {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(14px);
            border-radius: 22px;
            padding: 32px;
        }

        /* GOALS */
        #goals {
            padding: 80px 20px;
        }

        .goal-box {
            background: #fff;
            padding: 30px;
            border-radius: 22px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .1);
            text-align: center;
            transition: .3s;
        }

        .goal-box:hover {
            transform: translateY(-8px);
        }

        /* TESTIMONIAL SECTION */
        .testimonial-section {
            padding: 80px 0;
            background: #f4f6fb;
        }

        .testimonial-title {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 50px;
        }

        .testimonial-card {
            background: #fff;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: 0.3s;
            height: 100%;
        }

        .testimonial-card:hover {
            transform: translateY(-8px);
        }

        .testimonial-img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            border: 4px solid #6366f1;
        }

        .testimonial-name {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .testimonial-role {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 15px;
        }

        .testimonial-text {
            font-size: 0.95rem;
            color: #555;
        }

        /* ABOUT */
        #about {
            background: #fff;
            padding: 80px 20px;
        }

        .about-box {
            background: linear-gradient(135deg, #6366f1, #ec4899);
            color: #fff;
            padding: 50px;
            border-radius: 25px;
            text-align: center;
        }

        /* FOOTER */
        footer {
            background: #111;
            color: #fff;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>

<body>

    <header>
        <h1>College Kart 🛒</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="marketplace.php">Marketplace</a>

            <?php if ($userId): ?>
                <span>👤
                    <?= htmlspecialchars($userName) ?>
                </span>
                <a href="auth/logout.php">Logout</a>
            <?php else: ?>
                <a href="auth/login.php">Login</a>
                <a href="auth/signup.php">Sign Up</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- HERO -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 mb-4">
                    <h2>Buy | Sell | Donate</h2>
                    <p class="hero-subtext">Your one-stop shop for college essentials</p>
                    <a href="marketplace.php" class="btn-custom">Explore Marketplace</a>
                </div>

                <div class="col-lg-5">
                    <div class="hero-card">
                        <h3>🎓 College Kart</h3>
                        <p>A student-first marketplace to exchange essentials securely.</p>
                        <ul>
                            <li>✔ Verified students</li>
                            <li>✔ Fair pricing</li>
                            <li>✔ Donations supported</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- OUR GOALS -->
    <section id="goals">
        <div class="container">
            <h2 class="text-center mb-5 fw-bold">Our Goals</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="goal-box">
                        <h5>Affordable Campus Living</h5>
                        <p>Helping students buy & sell essentials easily.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="goal-box">
                        <h5>Secure Transactions</h5>
                        <p>Verified student accounts for safe exchange.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="goal-box">
                        <h5>Sustainability</h5>
                        <p>Encouraging reuse & donations across campus.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- WHAT STUDENTS SAY -->
    <section class="testimonial-section">
        <div class="container">
            <h2 class="testimonial-title">What Students Say 💬</h2>

            <div class="row g-4">

                <div class="col-md-4">
                    <div class="testimonial-card">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" class="testimonial-img" alt="Student">
                        <h5 class="testimonial-name">Aayush Thapaliya</h5>
                        <div class="testimonial-role">BCA</div>
                        <p class="testimonial-text">
                            College Kart helped me sell my old books easily.
                            Super smooth experience and trusted campus buyers!
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="testimonial-card">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" class="testimonial-img"
                            alt="Student">
                        <h5 class="testimonial-name">Prashant Kc</h5>
                        <div class="testimonial-role">BCA Student</div>
                        <p class="testimonial-text">
                            I found second-hand lab equipment at a great price.
                            It’s amazing to have a marketplace just for students.
                        </p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="testimonial-card">
                        <img src="https://randomuser.me/api/portraits/men/55.jpg" class="testimonial-img" alt="Student">
                        <h5 class="testimonial-name">Nayan Karki</h5>
                        <div class="testimonial-role">B.Sc IT</div>
                        <p class="testimonial-text">
                            I donated my old notes through College Kart.
                            Love the idea of helping juniors while decluttering!
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ABOUT -->
    <section id="about">
        <div class="container">
            <div class="about-box">
                <h2>About College Kart</h2>
                <p>College Kart is a secure student marketplace to buy, sell and donate items within your campus
                    community.</p>
            </div>
        </div>
    </section>

    <footer>
        <p>©
            <?= date('Y') ?> College Kart | Built by Prashant & Aayush
        </p>
    </footer>

</body>

</html>