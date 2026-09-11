<?php
session_start();
$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "booking_liburan";

$koneksi = mysqli_connect($host, $user, $password, $database);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

/** @var mysqli $koneksi */ // <-- Tambahkan baris ini

$error = false;

if (isset($_POST['login'])) {
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    $result = mysqli_query($koneksi, "SELECT * FROM user WHERE email = '$email'");

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {
            $_SESSION['login']   = true;
            $_SESSION['id_user'] = $row['id_user'];
            $_SESSION['nama']    = $row['nama'];
            $_SESSION['role']    = $row['role'];

            header("Location: dashboard.php");
            exit;
        }
    }

    $error = true;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 800px;
            max-width: 90%;
            height: 500px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            display: flex;
            overflow: hidden;
        }

        /* Bagian Kiri - Form Login */
        .login-section {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
        }

        .login-section h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 30px;
            color: #000;
        }

        .login-section form {
            width: 100%;
            max-width: 280px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .input-box {
            width: 100%;
            padding: 12px 16px;
            margin-bottom: 12px;
            background-color: #eeeeee;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            outline: none;
        }

        .forgot-pass {
            font-size: 12px;
            color: #444;
            text-decoration: none;
            margin: 15px 0 20px 0;
        }

        .btn-signin {
            background-color: #52b1fa;
            color: white;
            border: none;
            padding: 10px 32px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 13px;
            cursor: pointer;
            transition: 0.3s;
            text-transform: uppercase;
        }

        .btn-signin:hover {
            background-color: #3198e8;
        }

        /* Bagian Kanan - Banner Panel */
        .banner-section {
            flex: 1;
            background-color: #d8d8d8;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .banner-section h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 16px;
            color: #000;
        }

        .banner-section p {
            font-size: 13px;
            color: #222;
            line-height: 1.5;
            max-width: 260px;
            margin-bottom: 30px;
        }

        .btn-signup {
            background-color: #52b1fa;
            color: white;
            border: none;
            padding: 10px 28px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            transition: 0.3s;
        }

        .btn-signup:hover {
            background-color: #3198e8;
        }

        .error-msg {
            color: red;
            font-size: 13px;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="login-section">
            <h2>Sign in</h2>

            <?php if ($error): ?>
                <p class="error-msg">Email atau password salah!</p>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="email" name="email" class="input-box" placeholder="Email" required>
                <input type="password" name="password" class="input-box" placeholder="Pass" required>

                <a href="#" class="forgot-pass">Lupa kata sandi anda?</a>

                <!-- Ganti <button> menjadi <input type="submit"> -->
                <input type="submit" name="login" value="SIGN IN" class="btn-signin">
            </form>
        </div>

        <div class="banner-section">
            <h2>Selamat Datang!</h2>
            <p>Daftarkan diri anda dan mulai gunakan layanan kami segera.</p>
            <a href="register.php" class="btn-signup">Sign Up</a>
        </div>
    </div>

</body>

</html>