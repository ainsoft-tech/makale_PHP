<?php
// add_category.php
require_once 'config.php'; // Veritabanı bağlantısı

// Hata ve başarı mesajları için değişkenler
$error_message = '';
$success_message = '';

// Form gönderildiğinde çalışacak kod
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = trim($_POST['name']);
    
    // Kategori adı boş mu kontrol et
    if (empty($category_name)) {
        $error_message = 'Kategori adı boş bırakılamaz.';
    } else {
        // Kategori adı veritabanına ekle
        $stmt = $db->prepare("INSERT INTO categories (name) VALUES (:name)");
        $stmt->bindParam(':name', $category_name, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $success_message = 'Kategori başarıyla eklendi.';
            header('Location: index.php');
        } else {
            $error_message = 'Kategori eklenirken bir hata oluştu.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Kategori Ekle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Yeni Kategori Ekle</h2>

        <?php if ($error_message): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success" role="alert">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <form action="add_category.php" method="POST" class="mt-4">
            <div class="mb-3">
                <label for="name" class="form-label">Kategori Adı</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <button type="submit" class="btn btn-primary">Kaydet</button>
            <a href="index.php" class="btn btn-secondary">İptal</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
