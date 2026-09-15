<?php

$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "booking_liburan";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
if (isset($_POST['register'])) {
    $nama       = mysqli_real_escape_string($conn, $_POST['nama']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $password   = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $no_hp      = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $alamat     = mysqli_real_escape_string($conn, $_POST['alamat']);
    $role       = 'pelanggan';
    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');

    $cek_email = mysqli_query($conn, "SELECT * FROM user WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        echo "<script>alert('Email sudah terdaftar!');</script>";
    } else {
        $sql = "INSERT INTO user (nama, email, password, no_hp, alamat, role, created_at, updated_at) 
                VALUES ('$nama', '$email', '$password', '$no_hp', '$alamat', '$role', '$created_at', '$updated_at')";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Registrasi berhasil! Silakan login.'); location.href='login.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
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
            height: 520px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            display: flex;
            overflow: hidden;
        }

        /* Kiri - Banner Sambutan */
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

        .btn-signin {
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

        /* Kanan - Form Register */
        .register-section {
            flex: 1.2;
            padding: 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #ffffff;
        }

        .register-section h2 {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #000;
        }

        .register-section form {
            width: 100%;
            max-width: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .input-box {
            width: 100%;
            padding: 10px 14px;
            margin-bottom: 10px;
            background-color: #eeeeee;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            outline: none;
        }

        textarea.input-box {
            resize: none;
            height: 60px;
        }

        .btn-signup {
            background-color: #52b1fa;
            color: white;
            border: none;
            padding: 10px 32px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 13px;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
            text-transform: uppercase;
        }

        .btn-signup:hover, .btn-signin:hover {
            background-color: #3198e8;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="banner-section">
            <h2>Sudah Punya Akun?</h2>
            <p>Silakan masuk menggunakan akun yang telah terdaftar sebelumnya.</p>
            <a href="login.php" class="btn-signin">Sign In</a>
        </div>

        <div class="register-section">
            <h2>Sign Up</h2>

            <form method="POST" action="">
                <input type="text" name="nama" class="input-box" placeholder="Nama Lengkap" required>
                <input type="email" name="email" class="input-box" placeholder="Email" required>
                <input type="password" name="password" class="input-box" placeholder="Password" required>
                <input type="text" name="no_hp" class="input-box" placeholder="No. Handphone" required>
                <textarea name="alamat" class="input-box" placeholder="Alamat Lengkap" required></textarea>
                
                <button type="submit" name="register" class="btn-signup">SIGN UP</button>
            </form>
        </div>
    </div>

</body>
</html>