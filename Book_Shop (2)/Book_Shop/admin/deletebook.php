<?php

require_once "dbconnect.php";
if (!isset($_SESSION)) {
    session_start();
}
if (empty($_SESSION['loginSuccess'])) {
    header('Location: login.php');
    exit;
}

$bookId = 0;
if (isset($_GET['id']))      $bookId = (int)$_GET['id'];
elseif (isset($_GET['did'])) $bookId = (int)$_GET['did'];

if ($bookId <= 0) {
    header("Location: viewproduct.php");
    exit;
}

$deleteSuccess = null;
$errorMessage = null;

try {
    // get current image_url for local cleanup
    $q = $conn->prepare("SELECT image_url, title FROM books WHERE book_id = ?");
    $q->execute([$bookId]);
    $row = $q->fetch();
    $curImg = $row['image_url'] ?? null;
    $bookTitle = $row['title'] ?? 'Unknown';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
        $conn->beginTransaction();

        // remove category links
        $stmt = $conn->prepare("DELETE FROM book_categories WHERE book_id = ?");
        $stmt->execute([$bookId]);

        // remove the book
        $stmt = $conn->prepare("DELETE FROM books WHERE book_id = ?");
        $stmt->execute([$bookId]);

        $conn->commit();

        // delete local file if path looks local
        if ($curImg && str_starts_with($curImg, 'uploads/')) {
            $fs = __DIR__ . '/' . $curImg;
            if (is_file($fs)) {
                @unlink($fs);
            }
        }

        $_SESSION['deleteSuccess'] = "Book <strong>" . htmlspecialchars($bookTitle) . "</strong> (ID $bookId) has been deleted.";
        header("Location: viewproduct.php");
        exit;
    }
} catch (Throwable $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    $errorMessage = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delete Book</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8fafc;
            min-height: 100vh;
        }

        .delete-container {
            max-width: 420px;
            margin: 80px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
            padding: 2.5rem 2rem 2rem 2rem;
            text-align: center;
        }

        .delete-icon {
            font-size: 3rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }

        .book-img {
            max-width: 120px;
            max-height: 160px;
            margin-bottom: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .btn-cancel {
            margin-left: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="delete-container">
        <div class="delete-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24">
                <path fill="#dc3545" d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm1 15h-2v-2h2v2Zm0-4h-2V7h2v6Z" />
            </svg>
        </div>
        <h2 class="mb-3">Delete Book</h2>
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
        <?php else: ?>
            <p class="mb-4">Are you sure you want to delete the following book?</p>
            <?php if ($curImg): ?>
                <img src="<?php echo htmlspecialchars($curImg); ?>" alt="Book Cover" class="book-img">
            <?php endif; ?>
            <h5 class="mb-2"><?php echo htmlspecialchars($bookTitle); ?></h5>
            <form method="post">
                <button type="submit" name="confirm_delete" class="btn btn-danger">Yes, Delete</button>
                <a href="viewproduct.php" class="btn btn-secondary btn-cancel">Cancel</a>
            </form>
        <?php endif; ?>
    </div>