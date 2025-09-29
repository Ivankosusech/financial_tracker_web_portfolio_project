<?php
session_start();
require_once 'db.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}
$user_id = $_SESSION['user_id'];

$message = '';

// Обработка добавления траты
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_expense'])) {
    $amount = floatval($_POST['amount']);
    $category = trim($_POST['category']);
    $comment = trim($_POST['comment'] ?? '');

    if ($amount <= 0) {
        $message = 'Сумма должна быть больше нуля';
    } elseif (empty($category)) {
        $message = 'Выберите категорию';
    } else {
        $stmt = $pdo->prepare("INSERT INTO expenses (user_id, amount, category, comment, date) VALUES (?, ?, ?, ?, date('now'))");
        $stmt->execute([$user_id, $amount, $category, $comment]);
        $message = 'Трата добавлена!';
        // Обновим данные ниже
    }
}

// Получаем траты
$expenses = $pdo->prepare("
    SELECT * FROM expenses 
    WHERE user_id = ? 
    ORDER BY date DESC, id DESC 
    LIMIT 20
");
$expenses->execute([$user_id]);
$expenses_list = $expenses->fetchAll();

// Статистика
$today = $pdo->prepare("SELECT SUM(amount) FROM expenses WHERE user_id = ? AND date = date('now')");
$today->execute([$user_id]);
$total_today = $today->fetchColumn() ?: 0;

$this_week = $pdo->prepare("SELECT SUM(amount) FROM expenses WHERE user_id = ? AND date >= date('now', '-6 days')");
$this_week->execute([$user_id]);
$total_week = $this_week->fetchColumn() ?: 0;

$top_category = $pdo->prepare("
    SELECT category, SUM(amount) as total 
    FROM expenses 
    WHERE user_id = ? 
    GROUP BY category 
    ORDER BY total DESC 
    LIMIT 1
");
$top_category->execute([$user_id]);
$top_cat = $top_category->fetch();
$top_category_name = $top_cat ? $top_cat['category'] : '—';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Дашборд — Finance Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        .header {
            background: linear-gradient(120deg, #4361ee, #3a0ca3);
            color: white;
            padding: 1.5rem 0;
        }
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }
        .stat-card {
            text-align: center;
            padding: 1.2rem;
        }
        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #4361ee;
        }
        .btn-logout {
            background: #ff6b6b;
            border: none;
        }
        .btn-logout:hover {
            background: #ff5252;
        }
    </style>
</head>
<body>
    <div class="header text-center">
        <div class="container">
            <h1>💰 Finance Tracker</h1>
            <p>Ваш личный финансовый помощник</p>
            <a href="logout.php" class="btn btn-logout btn-sm">Выйти</a>
        </div>
    </div>

    <div class="container mt-4">
        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <!-- Форма добавления -->
        <div class="card">
            <div class="card-body">
                <h5>Добавить трату</h5>
                <form method="POST">
                    <input type="hidden" name="add_expense" value="1">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="Сумма" required min="0.01">
                        </div>
                        <div class="col-md-4">
                            <select name="category" class="form-select" required>
                                <option value="">Категория</option>
                                <option value="еда">Еда</option>
                                <option value="транспорт">Транспорт</option>
                                <option value="развлечения">Развлечения</option>
                                <option value="здоровье">Здоровье</option>
                                <option value="другое">Другое</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <input type="text" name="comment" class="form-control" placeholder="Комментарий (опционально)">
                                <button type="submit" class="btn btn-primary">Добавить</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Статистика -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card">
                    <div>Сегодня</div>
                    <div class="stat-value"><?= number_format($total_today, 2, ',', ' ') ?> ₽</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card">
                    <div>За неделю</div>
                    <div class="stat-value"><?= number_format($total_week, 2, ',', ' ') ?> ₽</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card">
                    <div>Топ категория</div>
                    <div class="stat-value"><?= htmlspecialchars($top_category_name) ?></div>
                </div>
            </div>
        </div>

        <!-- Таблица трат -->
        <div class="card">
            <div class="card-body">
                <h5>Последние траты</h5>
                <?php if ($expenses_list): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Дата</th>
                                    <th>Категория</th>
                                    <th>Сумма</th>
                                    <th>Комментарий</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($expenses_list as $e): ?>
                                <tr>
                                    <td><?= htmlspecialchars($e['date']) ?></td>
                                    <td><?= htmlspecialchars($e['category']) ?></td>
                                    <td><?= number_format($e['amount'], 2, ',', ' ') ?> ₽</td>
                                    <td><?= htmlspecialchars($e['comment'] ?? '') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Пока нет трат. Добавьте первую!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>