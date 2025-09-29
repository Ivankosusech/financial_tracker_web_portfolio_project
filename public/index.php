<?php
// Никакой логики — просто лендинг
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finance Tracker — Твой личный помощник в финансах</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf9 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        .hero-section {
            padding: 4rem 0;
        }
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: white;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.12);
        }
        .btn-primary {
            background: linear-gradient(120deg, #4361ee, #3a0ca3);
            border: none;
            padding: 12px 28px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(120deg, #3a56e0, #2b0a8a);
            transform: scale(1.03);
        }
        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #4cc9f0, #4361ee);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            color: white;
            font-size: 1.5rem;
        }
        .contact-link {
            color: #4361ee;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        .contact-link:hover {
            color: #3a0ca3;
            text-decoration: underline;
        }
        footer {
            margin-top: 3rem;
            padding: 2rem 0;
            color: #6c757d;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Hero -->
        <section class="hero-section text-center">
            <h1 class="display-4 fw-bold mb-3">💰 Finance Tracker</h1>
            <p class="lead text-muted mb-4">
                Простой, безопасный и удобный инструмент для учёта личных финансов.<br>
                Отслеживай расходы, анализируй привычки и достигай финансовых целей.
            </p>
            <a href="auth.php" class="btn btn-primary btn-lg">Войти / Зарегистрироваться</a>
        </section>

        <!-- Features -->
        <section class="mb-5">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card p-4 h-100">
                        <div class="feature-icon mx-auto">📊</div>
                        <h5>Аналитика</h5>
                        <p class="text-muted">
                            Визуализация трат по категориям и времени. Понимай, куда уходят деньги.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-4 h-100">
                        <div class="feature-icon mx-auto">🔒</div>
                        <h5>Безопасность</h5>
                        <p class="text-muted">
                            Твои данные хранятся локально. Никаких облаков — только ты и твой бюджет.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card p-4 h-100">
                        <div class="feature-icon mx-auto">📱</div>
                        <h5>Telegram-бот</h5>
                        <p class="text-muted">
                            В будущем — добавление трат прямо из Telegram. Уже в разработке!
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Me -->
        <section class="text-center mb-5">
            <h2 class="mb-4">Об авторе</h2>
            <p class="text-muted mb-3">
                Этот проект создан как часть портфолио, демонстрирующего навыки в трёх ролях:
            </p>
            <div class="d-flex justify-content-center gap-4 flex-wrap">
                <span class="badge bg-primary">Веб-разработка (PHP)</span>
                <span class="badge bg-success">Аналитика данных (Python)</span>
                <span class="badge bg-warning text-dark">Тестирование (QA)</span>
            </div>
        </section>

        <!-- Contacts -->
        <section class="text-center mb-5">
            <h3>Связаться со мной</h3>
            <div class="mt-3">
                <!-- 🔁 ЗАМЕНИ ЭТИ ССЫЛКИ НА СВОИ! -->
                <p>
                    <a href="https://t.me/your_telegram" class="contact-link" target="_blank">Telegram</a> •
                    <a href="mailto:your.email@example.com" class="contact-link">Email</a> •
                    <a href="https://github.com/your-username" class="contact-link" target="_blank">GitHub</a>
                </p>
            </div>
        </section>
    </div>

    <footer class="container text-center">
        <p class="mb-0">&copy; <?= date('Y') ?> Finance Tracker. Проект для портфолио.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>