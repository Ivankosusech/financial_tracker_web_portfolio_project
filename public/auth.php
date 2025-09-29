<?php
session_start();
require_once 'db.php';

$message = '';
$error = '';

// Обработка форм
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Некорректный email';
        } elseif (strlen($password) < 6) {
            $error = 'Пароль должен быть не короче 6 символов';
        } else {
            if ($action === 'login') {
                // Вход
                $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password_hash'])) {
                    $_SESSION['user_id'] = $user['id'];
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $error = 'Неверный email или пароль';
                }
            } elseif ($action === 'register') {
                // Регистрация
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = 'Пользователь с таким email уже существует';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, created_at) VALUES (?, ?, datetime('now'))");
                    $stmt->execute([$email, $hash]);
                    $message = 'Регистрация успешна! Теперь войдите.';
                }
            } elseif ($action === 'forgot') {
                // Восстановление (демо)
                $message = 'Ссылка для восстановления отправлена на ваш email (демо-режим).';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация — Finance Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        .auth-container {
            max-width: 500px;
            margin: 2rem auto;
        }
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            background: white;
        }
        .btn-primary {
            background: linear-gradient(120deg, #4361ee, #3a0ca3);
            border: none;
            padding: 12px;
            font-weight: 600;
        }
        .nav-pills .nav-link {
            color: #4361ee;
            font-weight: 600;
        }
        .nav-pills .nav-link.active {
            background: linear-gradient(120deg, #4361ee, #3a0ca3);
            color: white;
        }
        .form-section {
            display: none;
        }
        .form-section.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container auth-container">
        <div class="text-center mb-4">
            <h2>🔐 Авторизация</h2>
            <p class="text-muted">Войдите или создайте аккаунт</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Переключатели -->
        <ul class="nav nav-pills mb-4 justify-content-center" id="formSwitcher">
            <li class="nav-item">
                <a class="nav-link active" href="#" data-form="login">Вход</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-form="register">Регистрация</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" data-form="forgot">Забыли пароль?</a>
            </li>
        </ul>

        <!-- Формы -->
        <div class="card p-4">
            <!-- Форма входа -->
            <form method="POST" class="form-section active" id="form-login">
                <input type="hidden" name="action" value="login">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Пароль</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <button type="submit" class="btn btn-primary w-100">Войти</button>
            </form>

            <!-- Форма регистрации -->
            <form method="POST" class="form-section" id="form-register">
                <input type="hidden" name="action" value="register">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Пароль (мин. 6 символов)</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <button type="submit" class="btn btn-primary w-100">Зарегистрироваться</button>
            </form>

            <!-- Форма восстановления -->
            <form method="POST" class="form-section" id="form-forgot">
                <input type="hidden" name="action" value="forgot">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Отправить ссылку</button>
            </form>
        </div>

        <div class="text-center mt-3">
            <a href="index.php" class="text-muted">← Вернуться на главную</a>
        </div>
    </div>

    <script>
        // Переключение форм
        document.querySelectorAll('#formSwitcher a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const formId = this.getAttribute('data-form');
                
                // Убираем активность со всех
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                document.querySelectorAll('.form-section').forEach(f => f.classList.remove('active'));
                
                // Добавляем активность
                this.classList.add('active');
                document.getElementById('form-' + formId).classList.add('active');
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>