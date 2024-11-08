<?php
// delete_article.php
require_once 'config.php';

// Check if an ID is provided
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

try {
    // Get the article and check if it exists
    $stmt = $db->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$article) {
        $_SESSION['error'] = "Makale bulunamadı!";
        header('Location: index.php');
        exit;
    }

    // If there is an image, delete it from the uploads directory
    if ($article['image'] && file_exists("uploads/" . $article['image'])) {
        unlink("uploads/" . $article['image']);
    }

    // Delete the article from the database
    $stmt = $db->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->execute([$id]);

    // Set success message and redirect
    $_SESSION['success'] = "Makale başarıyla silindi!";
    header('Location: index.php');
    exit;

} catch (Exception $e) {
    // Set error message and redirect in case of an error
    $_SESSION['error'] = "Makale silinirken bir hata oluştu: " . $e->getMessage();
    header('Location: index.php');
    exit;
}
?>
