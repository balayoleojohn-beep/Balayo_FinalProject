
<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $feedback_id = filter_input(
        INPUT_POST,
        'feedback_id',
        FILTER_VALIDATE_INT
    );

    $commenter_name = trim($_POST['commenter_name'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

    if (
        $feedback_id &&
        $feedback_id > 0 &&
        $commenter_name !== '' &&
        $comment !== ''
    ) {

        $stmt = $pdo->prepare("
            SELECT id FROM feedback WHERE id = ?
        ");

        $stmt->execute([$feedback_id]);

        if ($stmt->fetch()) {

            $stmt = $pdo->prepare("
                INSERT INTO comments
                (feedback_id, commenter_name, comment)
                VALUES (?, ?, ?)
            ");

            $stmt->execute([
                $feedback_id,
                $commenter_name,
                $comment
            ]);
        }
    }
}

header("Location: index.php");
exit;
?>
