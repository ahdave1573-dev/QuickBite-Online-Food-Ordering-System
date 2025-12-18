<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cart_item_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        if (isset($item['quantity'])) {
            $cart_item_count += $item['quantity'];
        }
    }
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title ?? 'QuickBite') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Montserrat:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --primary-color: #ff5722;
            --primary-dark: #e64a19;
            --bg-color: #fff9f0;
            --surface-color: #ffffff;
            --text-dark: #333333;
            --text-light: #ffffff;
            --footer-bg: #222;
            --border-color: #e0e0e0;
            --shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
            --font-heading: 'Montserrat', sans-serif;
            --font-body: 'Poppins', sans-serif;
        }
        body {
            font-family: var(--font-body);
            margin: 0;
            background-color: var(--bg-color);
            color: var(--text-dark);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex-grow: 1;
        }
        .header {
            background-color: var(--primary-color);
            padding: 12px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-light);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .header-left {
            display: flex;
            align-items: center;
        }
        .logo {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            margin-right: 15px;
        }
        .site-name {
            font-family: var(--font-heading);
            font-size: 26px;
            color: var(--text-light);
            text-decoration: none;
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .nav-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .main-nav {
            display: flex;
            gap: 5px;
        }
        .main-nav a, .auth-links a {
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            padding: 10px 15px;
            border-radius: 6px;
            transition: background-color 0.3s ease, color 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .main-nav a:hover, .auth-links a:hover {
            background-color: var(--primary-dark);
        }

        .main-nav a.active {
            background-color: var(--primary-dark);
            font-weight: 600;
        }
        .logout-btn {
            background-color: rgba(0,0,0,0.15);
        }
        .logout-btn:hover {
            background-color: #c0392b !important;
        }

        .cart-link {
            gap: 8px;
        }
        .icon-wrapper {
            position: relative;
        }
        .cart-link .fa-shopping-cart {
            font-size: 1.2rem;
        }
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -8px;
            background-color: #c0392b;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7rem;
            font-weight: bold;
            border: 2px solid var(--primary-color);
        }

        .mobile-nav-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }

        @media (max-width: 992px) {
            .header {
                padding: 12px 20px;
            }
            .mobile-nav-toggle {
                display: block;
                z-index: 1001; 
            }
            .nav-wrapper {
                position: fixed;
                top: 0;
                right: 0;
                width: 280px;
                height: 100%;
                background-color: #333;
                flex-direction: column;
                align-items: flex-start;
                padding: 80px 20px 20px;
                gap: 15px;
                transform: translateX(100%);
                transition: transform 0.3s ease-in-out;
            }
            .nav-wrapper.active {
                transform: translateX(0);
            }
            .main-nav {
                flex-direction: column;
                width: 100%;
                gap: 10px;
            }
            .auth-links {
                flex-direction: column;
                width: 100%;
                gap: 10px;
                padding-top: 20px;
                border-top: 1px solid #555;
            }
            .main-nav a, .auth-links a {
                padding: 12px 15px;
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>

<header class="header">
    <div class="header-left">
        <a href="index.php" style="text-decoration: none; display: flex; align-items: center;">
            <img src="images/Logo.jpg" alt="Logo" class="logo">
            <span class="site-name">QuickBite</span>
        </a>
    </div>

    <div class="header-right">
        <div class="nav-wrapper">
            <nav class="main-nav">
                <a href="index.php" class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">Home</a>
                <a href="about.php" class="<?= ($current_page == 'about.php') ? 'active' : '' ?>">About</a>
                <a href="menu.php" class="<?= ($current_page == 'menu.php') ? 'active' : '' ?>">Menu</a>
                <a href="offers.php" class="<?= ($current_page == 'offers.php') ? 'active' : '' ?>">Offers</a>
                <a href="contact.php" class="<?= ($current_page == 'contact.php') ? 'active' : '' ?>">Contact</a>
            </nav>

            <div class="auth-links">
                <?php if (isset($_SESSION['username'])): ?>
                    <a href="profile.php" class="<?= ($current_page == 'profile.php') ? 'active' : '' ?>">Profile</a>
                    
                    <a href="cart.php" title="View Cart" class="cart-link <?= ($current_page == 'cart.php') ? 'active' : '' ?>">
                        <span class="icon-wrapper">
                            <i class="fa-solid fa-shopping-cart"></i>
                            <?php if ($cart_item_count > 0): ?>
                                <span class="cart-badge"><?= $cart_item_count ?></span>
                            <?php endif; ?>
                        </span>
                        <span>Cart</span>
                    </a>
                    
                    <a href="logout.php" class="logout-btn">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="<?= ($current_page == 'login.php') ? 'active' : '' ?>">Login</a>
                <?php endif; ?>
            </div>
        </div>

        <button class="mobile-nav-toggle" aria-label="Toggle navigation">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>
</header>

<main>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.querySelector('.mobile-nav-toggle');
    const navWrapper = document.querySelector('.nav-wrapper');

    if (toggleBtn && navWrapper) {
        toggleBtn.addEventListener('click', () => {
            navWrapper.classList.toggle('active');
        });
    }
});
</script>