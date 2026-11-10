
<?php
require 'db_connect.php';

$search = trim($_GET['search'] ?? '');

if ($search) {
    $stmt = $pdo->prepare("
        SELECT * FROM feedback
        WHERE name LIKE ? OR subject LIKE ? OR message LIKE ?
        ORDER BY id DESC
    ");
    $term = "%$search%";
    $stmt->execute([$term, $term, $term]);
} else {
    $stmt = $pdo->query("SELECT * FROM feedback ORDER BY id DESC");
}

$feedbacks = $stmt->fetchAll();

function getComments($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE feedback_id = ? ORDER BY id DESC");
    $stmt->execute([$id]);
    return $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Online Feedback System</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
* { font-family: 'Poppins', sans-serif; }
body { background: #0b1120; color: #dbeafe; min-height: 100vh; }

.navbar { background: linear-gradient(135deg,#111827,#172554); border-bottom: 1px solid #263858; padding: 18px 0; }
.navbar-brand { color: #67e8f9 !important; font-size: 22px; letter-spacing: 1px; }

.page-header { background: linear-gradient(135deg,#172554,#312e81); border: 1px solid #3730a3; border-radius: 16px; padding: 30px; margin-bottom: 30px; box-shadow: 0 0 25px #3b82f61a; }
.page-header h2 { color: #67e8f9; font-weight: 700; }
.page-header p { color: #a5b4fc; }

.form-control { background: #111827; border: 1px solid #334155; color: #e2e8f0; border-radius: 8px; }
.form-control:focus { background: #172033; color: #fff; border-color: #06b6d4; box-shadow: 0 0 10px #06b6d433; }
.form-control::placeholder { color: #64748b; }

.feedback-card { background: #111827; border: 1px solid #263858; border-radius: 14px; overflow: hidden; transition: .3s; }
.feedback-card:hover { transform: translateY(-4px); border-color: #06b6d4; box-shadow: 0 8px 25px #06b6d41a; }
.feedback-card .card-header { background: linear-gradient(135deg,#1e3a8a,#312e81); color: #67e8f9; padding: 18px; border-bottom: 1px solid #3730a3; }
.feedback-card .card-body { background: #111827; color: #cbd5e1; padding: 25px; }
.feedback-card strong { color: #67e8f9; }
.feedback-card p { overflow-wrap: anywhere; }
.text-muted { color: #94a3b8 !important; }

.comment-box { background: #0b1120; border: 1px solid #263858; border-left: 4px solid #8b5cf6; border-radius: 8px; padding: 15px; overflow-wrap: anywhere; }
.comment-box strong { color: #c4b5fd; }

.btn-primary { background: linear-gradient(135deg,#2563eb,#7c3aed); border: none; }
.btn-primary:hover { background: linear-gradient(135deg,#1d4ed8,#6d28d9); }
.btn-success { background: #0891b2; border: none; }
.btn-success:hover { background: #0e7490; }
.btn-warning { color: #111827; border: none; }
.btn-danger { background: #be123c; border: none; }
.btn-outline-danger { color: #fb7185; border-color: #be123c; }
.btn-outline-danger:hover { background: #be123c; color: #fff; }
.btn-secondary { background: #334155; border: none; }

hr { border-color: #334155; opacity: 1; margin: 25px 0; }
.alert-info { background: #172554; border: 1px solid #1d4ed8; color: #93c5fd; }
footer { background: #080d18; color: #64748b; border-top: 1px solid #263858; }

@media (max-width: 768px) {
    .page-header { padding: 20px; }
    .page-header h2 { font-size: 22px; }
    .feedback-card .card-body { padding: 18px; }
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

<div class="container mt-4 mb-5">

    <div class="page-header">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h2>Feedback Dashboard</h2>
                <p class="mb-0">Share your thoughts and join the conversation.</p>
            </div>
            <a href="add_feedback.php" class="btn btn-primary btn-lg">+ Submit Feedback</a>
        </div>
    </div>

    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control"
                   placeholder="Search feedback, subject, or name..."
                   value="<?= e($search) ?>">
            <button class="btn btn-primary">Search</button>
            <a href="index.php" class="btn btn-secondary">Clear</a>
        </div>
    </form>

    <div class="mb-4">
        <h5 class="text-info">Recent Feedback</h5>
        <p class="text-muted"><?= count($feedbacks) ?> feedback record(s) found</p>
    </div>

    <?php if ($feedbacks): ?>
        <?php foreach ($feedbacks as $feedback): ?>

            <div class="card feedback-card mb-4">

                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0 fw-semibold"><?= e($feedback['subject']) ?></h5>
                        <span class="badge bg-dark">#<?= $feedback['id'] ?></span>
                    </div>
                </div>

                <div class="card-body">

                    <p><strong>👤 Name:</strong> <?= e($feedback['name']) ?></p>
                    <p><strong>✉ Email:</strong> <?= e($feedback['email']) ?></p>
                    <p><strong>💬 Feedback:</strong><br><?= nl2br(e($feedback['message'])) ?></p>

                    <small class="text-muted">Submitted: <?= e($feedback['created_at']) ?></small>

                    <div class="mt-3">
                        <a href="edit_feedback.php?id=<?= $feedback['id'] ?>" class="btn btn-warning btn-sm">✏ Edit</a>
                        <a href="delete_feedback.php?id=<?= $feedback['id'] ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Delete this feedback and its comments?')">🗑 Delete</a>
                    </div>

                    <hr>
                    <h6 class="fw-bold text-info mb-3">💭 Comments</h6>

                    <?php $comments = getComments($pdo, $feedback['id']); ?>

                    <?php if ($comments): ?>
                        <?php foreach ($comments as $comment): ?>

                            <div class="comment-box mb-3">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <strong><?= e($comment['commenter_name']) ?></strong>
                                    <small class="text-muted"><?= e($comment['created_at']) ?></small>
                                </div>

                                <p class="mt-2 mb-2"><?= nl2br(e($comment['comment'])) ?></p>

                                <a href="delete_comment.php?id=<?= $comment['id'] ?>"
                                   class="btn btn-outline-danger btn-sm"
                                   onclick="return confirm('Delete this comment?')">Delete Comment</a>
                            </div>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted">No comments yet. Be the first to comment!</p>
                    <?php endif; ?>

                    <form action="add_comment.php" method="POST" class="mt-3">
                        <input type="hidden" name="feedback_id" value="<?= $feedback['id'] ?>">

                        <div class="mb-2">
                            <input type="text" name="commenter_name" class="form-control"
                                   placeholder="Enter your name..." maxlength="100" required>
                        </div>

                        <div class="mb-2">
                            <textarea name="comment" class="form-control"
                                      placeholder="Write your comment..." rows="3" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-success btn-sm">+ Add Comment</button>
                    </form>

                </div>
            </div>

        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info">
            No feedback found. Try another search or submit new feedback.
        </div>
    <?php endif; ?>

</div>

<footer class="text-center mt-5 p-4">
    <p class="mb-1 fw-semibold">FeedbackHub</p>
    <small>BY:Leo John Balayo &copy; <?= date('Y') ?></small>
</footer>

</body>
</html>
