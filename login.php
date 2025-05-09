<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sara's Pretty Picks</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f5f0f6;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        header {
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            color: #9c27b0;
            font-size: 1.8rem;
            font-weight: 700;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        nav ul li a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }

        nav ul li a:hover,
        nav ul li a.active {
            color: #ec407a;
        }

        main.container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 200px);
        }

        .auth-container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
        }

        .auth-form {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .auth-form.floating:hover {
            transform: translateY(-5px);
        }

        .auth-form h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #9c27b0;
            font-size: 1.8rem;
        }

        .alert.error {
            background-color: #fce4ec;
            color: #ad1457;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #ec407a;
            box-shadow: 0 0 5px rgba(236, 64, 122, 0.3);
        }

        .form-group input::placeholder {
            color: #aaa;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background-color: #9c27b0;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .btn:hover {
            background-color: #7b1fa2;
            transform: translateY(-2px);
        }

        .auth-form p {
            text-align: center;
            margin-top: 1rem;
            color: #555;
        }

        .auth-form p a {
            color: #ec407a;
            text-decoration: none;
            font-weight: 500;
        }

        .auth-form p a:hover {
            text-decoration: underline;
        }

        footer {
            background-color: #fff;
            padding: 1rem 0;
            text-align: center;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }

        footer p {
            color: #555;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            header .container {
                flex-direction: column;
                gap: 10px;
            }

            nav ul {
                flex-direction: column;
                align-items: center;
            }

            .auth-container {
                padding: 10px;
            }

            .auth-form {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <h1>Sara's Pretty Picks</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="login.php" class="active">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="auth-container">
            <form class="auth-form floating" method="POST">
                <h2>Welcome Back!</h2>

                <?php if (isset($error)): ?>
                    <div class="alert error"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required placeholder="Enter your username">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>

                <button type="submit" class="btn">Login</button>

                <p>Don't have an account? <a href="register.php">Create one</a></p>
            </form>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>© <?php echo date('Y'); ?> Sara's Pretty Picks. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Add focus effects
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentNode.style.transform = 'scale(1.02)';
            });

            input.addEventListener('blur', function() {
                this.parentNode.style.transform = 'scale(1)';
            });
        });
    </script>
</body>

</html>