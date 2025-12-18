<?php
session_start();

$conn = new mysqli("localhost", "root", "", "food");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? '');
    $password = trim($_POST["password"] ?? '');

    if ($username && $password) {

        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
        $stmt->close();
    } else {
        $error = "Please enter both username and password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - QuickBite</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); 
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }

        .login-container {
            width: 380px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .login-container h2 {
            text-align: center;
            color: #333;
            font-weight: 600;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="text"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s; 
        }

        button:hover {
            transform: translateY(-2px);
        }

        button[type="submit"] {
            background-color: #007bff;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        .register-link button {
            background-color: #28a745;
        }
        
        .register-link button:hover {
             background-color: #218838;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #555;
        }

        .register-link p {
            margin-bottom: 10px;
        }

        .error-message {
            color: #D8000C;
            background-color: #FFD2D2;
            border: 1px solid #D8000C;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        footer {
            background-color: #343a40;
            color: #f8f9fa;
            width: 100%;
        }

        footer a {
            color: #f8f9fa;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        footer a:hover {
            color: #007bff;
        }

        .footer-flex {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 30px 20px;
            max-width: 1200px;
            margin: 0 auto;
            text-align: left;
        }

        .footer-flex div {
            flex: 1;
            min-width: 250px;
            padding: 10px 20px;
        }

        .footer-flex h3 {
             color: #ffffff;
             border-bottom: 2px solid #007bff;
             padding-bottom: 10px;
             margin-bottom: 15px;
             display: inline-block;
        }
        
        .footer-social img {
            width: 32px;
            margin-right: 15px;
            transition: transform 0.2s;
        }
        
        .footer-social img:hover {
            transform: scale(1.1);
        }

        .footer-bottom {
            text-align: center;
            padding: 15px 0;
            background-color: #212529;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="login-container">
        <h2>Login to QuickBite</h2>

        <?php if ($error): ?>
            <p class="error-message"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required>

            <button type="submit">Login</button>
        </form>

        <div class="register-link">
            <p>Don't have an account?</p>
            <a href="create_user.php">
                <button>Create Account</button>
            </a>
        </div>
    </div>
</div>

</body>
</html>