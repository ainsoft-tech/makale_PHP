<?php
// edit_article.php
require_once 'config.php';

// Mevcut makale verilerini alabilmek için makale ID'sini kontrol et
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

// Yazarlar ve kategoriler listesi için veritabanı sorguları
$authors = [];
$categories = [];
try {
    $stmt = $db->query("SELECT id, name FROM authors");
    $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $db->query("SELECT id, name FROM categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Veritabanından veri alınırken bir hata oluştu: " . $e->getMessage());
}

// Mevcut makale verilerini getir
try {
    $stmt = $db->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$article) {
        header('Location: index.php');
        exit;
    }
} catch (Exception $e) {
    die("Makale alınırken bir hata oluştu: " . $e->getMessage());
}

$message = '';

// Form gönderildiğinde makale verilerini güncelle
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $author_id = trim($_POST['author_id']);
        $category_id = trim($_POST['category_id']);
        
        if (empty($title) || empty($content) || empty($author_id) || empty($category_id)) {
            throw new Exception("Lütfen tüm alanları doldurun.");
        }

        // Yeni bir resim yüklenmişse işle
        $image = $article['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['image']['name'];
            $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!in_array($filetype, $allowed)) {
                throw new Exception("Sadece JPG, JPEG, PNG & GIF dosyaları yüklenebilir.");
            }

            if ($_FILES['image']['size'] > 5000000) {
                throw new Exception("Dosya boyutu çok büyük (max 5MB).");
            }

            $newname = uniqid() . '.' . $filetype;
            $uploadPath = 'uploads/' . $newname;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                throw new Exception("Dosya yükleme hatası.");
            }

            // Eski resmi sil
            if ($article['image'] && file_exists("uploads/" . $article['image'])) {
                unlink("uploads/" . $article['image']);
            }

            $image = $newname;
        }

        // Makale güncelleme sorgusu
        $stmt = $db->prepare("UPDATE articles SET title = :title, content = :content, author_id = :author_id, category_id = :category_id, image = :image WHERE id = :id");
        $stmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':author_id' => $author_id,
            ':category_id' => $category_id,
            ':image' => $image,
            ':id' => $id
        ]);

        // Başarı mesajı ve yönlendirme
        $_SESSION['success'] = "Makale başarıyla güncellendi!";
        header('Location: index.php');
        exit;

    } catch(Exception $e) {
        $message = '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Makale Düzenle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Makale Yönetimi</a>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title m-0">Makale Düzenle</h3>
                    </div>
                    <div class="card-body">
                        <?php if($message): ?>
                            <?php echo $message; ?>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Başlık</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo htmlspecialchars($article['title']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">İçerik</label>
                                <textarea class="form-control" id="content" name="content" rows="10" required><?php echo htmlspecialchars($article['content']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="author_id" class="form-label">Yazar</label>
                                <select class="form-select" id="author_id" name="author_id" required>
                                    <option value="">Yazar Seçin</option>
                                    <?php foreach ($authors as $author): ?>
                                        <option value="<?php echo $author['id']; ?>" <?php echo ($article['author_id'] == $author['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($author['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">Kategori</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Kategori Seçin</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo ($article['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Yeni Resim (isteğe bağlı)</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <small class="text-muted">Desteklenen formatlar: JPG, JPEG, PNG, GIF (Max: 5MB)</small>
                                <?php if ($article['image']): ?>
                                    <div class="mt-2">
                                        <img src="uploads/<?php echo htmlspecialchars($article['image']); ?>" width="150" alt="Mevcut Resim">
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Güncelle</button>
                                <a href="index.php" class="btn btn-secondary">İptal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        CKEDITOR.replace('content');
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
