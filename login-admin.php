<?php
session_start();
require_once 'config.php';

if(isset($_SESSION['admin_logged_in'])) {
    header("Location: admin.php");
    exit();
}

if(isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $user['username'];
            header("Location: admin.php");
            exit();
        } else {
            $error = "Username atau password salah!";
        }
    } catch(PDOException $e) {
        $error = "Terjadi kesalahan saat login. Silakan coba lagi.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - MBKM UPJ</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: solid #f5f7fa;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }
        
        .login-container {
            background: rgba(240, 240, 240, 0.95);
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            position: relative;
            overflow: hidden;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header img {
            width: 120px;
            margin-bottom: 1rem;
        }

        .login-header h2 {
            color: #3e41ff;
            font-size: 1.8rem;
            margin: 0;
            padding: 0;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            padding-left: 2.5rem;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-group input:focus {
            border-color: #3e41ff;
            outline: none;
            box-shadow: 0 0 0 3px rgba(62, 65, 255, 0.1);
        }

        .form-group i {
            position: absolute;
            left: 0.75rem;
            top: 2.3rem;
            color: #999;
        }

        .login-btn {
            width: 100%;
            padding: 0.9rem;
            background: #3e41ff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .login-btn:hover {
            background: #0012b3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(62, 65, 255, 0.3);
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #eee;
            font-size: 0.9rem;
        }

        .login-footer a {
            color: #3e41ff;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .login-footer a:hover {
            color: #0012b3;
        }

        .error {
            background: #ffe3e3;
            color: #dc3545;
            padding: 0.75rem;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .success {
            background: #e3ffe4;
            color: #28a745;
            padding: 0.75rem;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .back-to-home {
    position: absolute;
    top: 1rem;
    left: 1rem;
    color: black;
    text-decoration: none;
    display: flex;
    align-items: center;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.back-to-home i {
    margin-right: 0.5rem;
}

.back-to-home:hover {
    transform: translateX(-3px);
}
    </style>
</head>
<body>
    <a href="index.html" class="back-to-home">
        <i class="fas fa-arrow-left"></i>
        Back to Home
    </a>

    <div class="login-container">
        <div class="login-header">
            <img src="assets/upj-no-bg.png" alt="UPJ Logo">
        </div>

        <?php if(isset($error)): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="success">
                <i class="fas fa-check-circle"></i>
                <?php echo $_SESSION['success_message']; ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username Admin</label>
                <i class="fas fa-user"></i>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password Admin</label>
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" name="login" class="login-btn">
                <i class="fas fa-sign-in-alt"></i>
                Login
            </button>
        </form>

    </div>
</body>
</html>
