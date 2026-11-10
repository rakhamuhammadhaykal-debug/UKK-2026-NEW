<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pelanggaran Siswa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a, #1e3a8a);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
        }

        .logo {
            width: 75px;
            height: 75px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 30px;
            font-weight: bold;
        }

        h1 {
            text-align: center;
            color: #111827;
            font-size: 27px;
            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: #374151;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .input-box {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 17px;
        }

        input {
            width: 100%;
            height: 50px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 0 15px 0 45px;
            font-size: 14px;
            outline: none;
            transition: 0.2s;
        }

        input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .login-button {
            width: 100%;
            height: 50px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 5px;
        }

        .login-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }

        .error-message {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 14px;
            margin-bottom: 20px;
            text-align: center;
        }

        .footer {
            text-align: center;
            color: #9ca3af;
            font-size: 12px;
            margin-top: 25px;
        }

        .footer strong {
            color: #2563eb;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 30px 25px;
            }
            h1 {
                font-size: 23px;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <div class="logo">
            PS
        </div>

        <h1>
            Selamat Datang
        </h1>

        <p class="subtitle">
            Login ke Aplikasi Pelanggaran Siswa
        </p>

        <?php
        if (isset($_SESSION['pesan_error'])) {
            echo '<div class="error-message">'
                . htmlspecialchars($_SESSION['pesan_error'])
                . '</div>';
            unset($_SESSION['pesan_error']);
        }
        ?>

        <!-- FORM LOGIN -->
        <form action="proses_login.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-box">
                    <span class="input-icon">✉</span>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        placeholder="Masukkan email"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-box">
                    <span class="input-icon">🔒</span>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        placeholder="Masukkan password"
                        required
                    >
                </div>
            </div>

            <button type="submit" class="login-button">
                Login
            </button>
        </form>

        <div class="footer">
            © 2026
            <strong>
                Pelanggaran Siswa
            </strong>
        </div>
    </div>
</div>

</body>
</html>