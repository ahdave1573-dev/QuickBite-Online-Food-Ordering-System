<?php

$page_title = "Contact Us - QuickBite";
require_once 'header.php';

$conn = new mysqli("localhost", "root", "", "food");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$name = $email = $subject = $message = "";
$success = $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");

    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $subject && $message) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssss", $name, $email, $subject, $message);
            if ($stmt->execute()) {
                $success = "Thank you for contacting us, " . htmlspecialchars($name) . "! We will get back to you soon.";
                $name = $email = $subject = $message = ""; 
            } else {
                $error = "Database error. Please try again.";
            }
            $stmt->close();
        }
    } else {
        $error = "Please fill in all fields with valid information.";
    }
}
$conn->close();
?>

<style>

    .main-container { display: flex; flex-wrap: wrap; justify-content: center; align-items: flex-start; padding: 50px 20px; gap: 40px; max-width: 1200px; margin: auto; }
    .form-container, .contact-info { flex: 1; min-width: 320px; max-width: 550px; }
    form, .contact-info-card { background: var(--surface-color); padding: 35px; border-radius: 12px; box-shadow: 0 6px 20px rgba(0,0,0,0.07); border: 1px solid #f0f0f0; }
    h2 { font-size: 2.2em; color: var(--primary-dark); margin-bottom: 15px; text-align: center; }
    .subtitle { text-align: center; margin-bottom: 30px; color: #777; }
    h3 { font-size: 1.6em; color: var(--primary-dark); margin-bottom: 25px; text-align: center; }
    label { display: block; margin-bottom: 8px; font-weight: 500; color: #555; }
    input, textarea { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 1em; font-family: 'Poppins', sans-serif; transition: border-color 0.3s, box-shadow 0.3s; }
    input:focus, textarea:focus { outline: none; border-color: var(--primary-color); box-shadow: 0 0 0 3px rgba(255, 87, 34, 0.2); }
    button[type="submit"] { background: var(--primary-color); color: white; padding: 14px 25px; font-size: 1.1em; font-weight: 600; border: none; border-radius: 8px; cursor: pointer; width: 100%; transition: background-color 0.3s ease, transform 0.2s ease; }
    button[type="submit"]:hover { background-color: var(--primary-dark); transform: translateY(-2px); }
    .success, .error { padding: 15px; margin-bottom: 20px; border-radius: 8px; text-align: center; }
    .success { color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; }
    .error { color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; }
    .info-item { display: flex; align-items: flex-start; gap: 15px; margin-bottom: 20px; }
    .info-item .icon { font-size: 1.5em; color: var(--primary-color); margin-top: 3px; }
    .info-item .details { font-size: 1em; line-height: 1.6; color: #555; }
    .info-item strong { color: var(--text-dark); display: block; font-weight: 600; }
</style>

<div class="main-container">
    <div class="form-container">
        <form method="POST" action="contact.php">
            <h2>Get in Touch</h2>
            <p class="subtitle">We'd love to hear from you. Fill out the form below.</p>
            <?php if ($success): ?><p class="success"><?= $success ?></p><?php endif; ?>
            <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
            <label for="name">Your Name:</label>
            <input type="text" id="name" name="name" placeholder="Enter your full name" value="<?= htmlspecialchars($name) ?>" required>
            <label for="email">Your Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email address" value="<?= htmlspecialchars($email) ?>" required>
            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" placeholder="What is this about?" value="<?= htmlspecialchars($subject) ?>" required>
            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="5" placeholder="Write your message here..." required><?= htmlspecialchars($message) ?></textarea>
            <button type="submit">Send Message</button>
        </form>
    </div>
    <div class="contact-info">
        <div class="contact-info-card">
            <h3>Contact Information</h3>
            <div class="info-item"><span class="icon">📍</span><div class="details"><strong>Address</strong>123 Food Street, Rajkot, Gujarat, India</div></div>
            <div class="info-item"><span class="icon">📞</span><div class="details"><strong>Phone</strong>+91 88499 19418</div></div>
            <div class="info-item"><span class="icon">✉️</span><div class="details"><strong>Email</strong>support@quickbite.com</div></div>
            <div class="info-item"><span class="icon">⏰</span><div class="details"><strong>Hours</strong>Mon - Sat: 10:00 AM - 10:00 PM</div></div>
        </div>
    </div>
</div>

<?php

require_once 'footer.php';
?>
