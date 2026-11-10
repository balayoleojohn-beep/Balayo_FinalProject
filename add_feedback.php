```php
<?php
require 'db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email || !$subject || !$message) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO feedback (name, email, subject, message)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$name, $email, $subject, $message]);

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Feedback</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Poppins', sans-serif; }

        body {
            background: #0b1120;
            color: #dbeafe;
            min-height: 100vh;
        }

        .navbar, footer {
            background: #111827;
            border-color: #263858;
        }

        .navbar { padding: 18px 0; }
        .navbar-brand { color: #67e8f9 !important; }

        .form-card {
            max-width: 650px;
            background: #111827;
            border: 1px solid #263858;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 8px 25px #06b6d41a;
        }

        .card-header {
            background: linear-gradient(135deg, #1e3a8a, #312e81);
            color: #67e8f9;
            padding: 20px;
        }

        .card-body { padding: 30px; }
        .form-label { color: #67e8f9; font-weight: 500; }

        .form-control {
            background: #0b1120;
            color: #e2e8f0;
            border: 1px solid #334155;
            border-radius: 8px;
        }

        .form-control:focus {
            background: #172033;
            color: #fff;
            border-color: #06b6d4;
            box-shadow: 0 0 10px #06b6d433;
        }

        .form-control::placeholder { color: #64748b; }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            border: none;
        }

        .btn-primary:hover { background: #4338ca; }
        .btn-secondary { background: #334155; border: none; }

        .alert-danger {
            background: #3f1725;
            border: 1px solid #be123c;
            color: #fda4af;
        }

        footer {
            color: #64748b;
            border-top: 1px solid #263858;
        }

        @media (max-width: 768px) {
            .card-body { padding: 20px; }
        }
    </style>
</head>

<body>

<nav class="navbar navbar-dark">
    <div class="container justify-content-center">
        <a class="navbar-brand fw-bold text-center" href="index.php">
            Online Feedback and Comment System
        </a>
    </div>
</nav>

<div class="container my-5">
    <div class="card form-card mx-auto">

        <div class="card-header text-center">
            <h4 class="mb-1 fw-bold">✉ Submit Feedback</h4>
            <small>Share your thoughts and suggestions</small>
        </div>

        <div class="card-body">

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <?php
                $fields = [
                    'name' => ['Full Name', 100, 'text'],
                    'email' => ['Email Address', 150, 'email'],
                    'subject' => ['Subject', 200, 'text']
                ];

                foreach ($fields as $field => [$label, $max, $type]):
                ?>
                    <div class="mb-3">
                        <label class="form-label"><?= $label ?></label>
                        <input type="<?= $type ?>" name="<?= $field ?>"
                               class="form-control" maxlength="<?= $max ?>"
                               value="<?= e($_POST[$field] ?? '') ?>" required>
                    </div>
                <?php endforeach; ?>

                <div class="mb-4">
                    <label class="form-label">Feedback Message</label>
                    <textarea name="message" class="form-control"
                              rows="5" required><?= e($_POST['message'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Submit Feedback</button>
                <a href="index.php" class="btn btn-secondary">Back</a>
            </form>

        </div>
    </div>
</div>

<footer class="text-center mt-5 p-4">
    <p class="mb-1 fw-semibold">FeedbackHub</p>
    <small>Online Feedback and Comment System &copy; <?= date('Y') ?></small>
</footer>

</body>
</html>
```