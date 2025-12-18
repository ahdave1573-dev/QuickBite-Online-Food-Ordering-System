</main>
<footer>
    <div class="footer-container">
        <div class="footer-column">
            <h3>About Us</h3>
            <p>We are a passionate team serving delicious Indian cuisine. Quality, taste, and fast delivery is our promise!</p>
        </div>

        <div class="footer-column">
            <h3>Quick Links</h3>
            <ul class="footer-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="menu.php">Menu</a></li>
                <li><a href="offers.php">Offers</a></li>
                <li><a href="contact.php">Contact Us</a></li>
                <li><a href="privacy.php">Privacy Policy</a></li>
                <li><a href="terms.php">Terms of Service</a></li>
            </ul>
        </div>

        <div class="footer-column">
            <h3>Contact Us</h3>
            <p>Email: <a href="mailto:QuickBite@myfoodsite.com">QuickBite@myfoodsite.com</a></p>
            <p>Phone: <a href="tel:+918849919418">+91 8849919418</a></p>
            <p>Location: Rajkot, Gujarat, India</p>
        </div>
        <div class="footer-column">
            <h3>Follow Us</h3>
            <p class="social-icons">
                <a href="https://facebook.com" target="_blank"><img src="images/facebook.png" alt="facebook"></a>
                <a href="https://instagram.com" target="_blank"><img src="images/instagram.png" alt="instagram"></a>
                <a href="https://twitter.com" target="_blank"><img src="images/x.png" alt="twitter"></a>
            </p>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; <?= date("Y") ?> QuickBite. All rights reserved.
        <span class="footer-separator">|</span>
        <a href="admin/admin_login.php" class="admin-link">Admin Login</a>
    </div>
</footer>
<style>
    
    footer { 
        background-color: var(--footer-bg, #343a40);
        color: #eee; 
        padding-top: 40px; 
    }
    .footer-container { 
        display: flex; 
        flex-wrap: wrap; 
        justify-content: space-around; 
        max-width: 1200px; 
        margin: 0 auto; 
        text-align: left; 
    }
    .footer-column { 
        flex: 1; 
        min-width: 220px; 
        padding: 10px 20px; 
        margin-bottom: 20px;
    }
    .footer-column h3 { 
        color: var(--primary-color); 
        margin-bottom: 20px; 
        border-bottom: 2px solid var(--primary-color); 
        padding-bottom: 10px; 
        display: inline-block; 
        font-size: 1.2em;
    }
    .footer-column p { 
        line-height: 1.8; 
        color: #ccc;
    }
    .footer-column a { 
        color: #ccc; 
        transition: color 0.3s; 
        text-decoration: none; 
    }
    .footer-column a:hover { 
        color: white; 
    }
    .footer-column .social-icons img { 
        width: 32px; 
        margin-right: 15px; 
        opacity: 0.8; 
        transition: opacity 0.3s, transform 0.3s; 
    }
    .footer-column .social-icons img:hover { 
        opacity: 1; 
        transform: scale(1.1); 
    }
    .footer-bottom { 
        text-align: center; 
        padding: 20px 0; 
        margin-top: 20px; 
        border-top: 1px solid #444; 
        font-size: 0.9em; 
        color: #aaa;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .footer-links li {
        margin-bottom: 12px;
    }
    .footer-links a {
        display: flex;
        align-items: center;
    }
    .footer-links a::before {
        content: '»';
        margin-right: 8px;
        color: var(--primary-color);
    }
    .footer-separator {
        margin: 0 10px;
        color: #555;
    }
    .admin-link {
        color: #888;
        font-size: 0.9em;
        text-decoration: none;
    }
    .admin-link:hover {
        color: #ccc;
        text-decoration: underline;
    }
</style>
</body>
</html>