<?php
// add_article.php
require_once 'config.php';

// Uploads klasörünü kontrol et ve oluştur
$uploadsDir = 'uploads';
if (!file_exists($uploadsDir)) {
    mkdir($uploadsDir, 0777, true);
}

$message = ''; // Hata veya başarı mesajları için

// Yazarları veritabanından çek
$authors = [];
try {
    $stmt = $db->query("SELECT id, name FROM authors");
    $authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $message = '<div class="alert alert-danger">Yazarlar alınırken bir hata oluştu: ' . $e->getMessage() . '</div>';
}

// Kategorileri veritabanından çek
$categories = [];
try {
    $stmt = $db->query("SELECT id, name FROM categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $message = '<div class="alert alert-danger">Kategoriler alınırken bir hata oluştu: ' . $e->getMessage() . '</div>';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        $author_id = trim($_POST['author_id']); // Yazar ID'si
        $category_id = $_POST['category_id']; // Kategori ID'si

        // Temel doğrulama
        if (empty($title) || empty($content) || empty($author_id) || empty($category_id)) {
            throw new Exception("Lütfen tüm alanları doldurun.");
        }
        
        // Resim yükleme işlemi
        $image = '';
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
            $uploadPath = $uploadsDir . '/' . $newname;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                throw new Exception("Dosya yükleme hatası.");
            }
            $image = $newname;
        }
        
        // Veritabanına kaydet
        $stmt = $db->prepare("INSERT INTO articles (title, content, author_id, category_id, image) VALUES (:title, :content, :author_id, :category_id, :image)");
        $stmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':author_id' => $author_id,
            ':category_id' => $category_id,
            ':image' => $image
        ]);
        
        $_SESSION['success'] = "Makale başarıyla eklendi!";
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
    <title>Yeni Makale Ekle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- CKEditor CDN -->
    <script src="//cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <style>        
        /* CKEditor için özel bir sınıf ekleyerek yüksekliği kontrol edelim */
        .ck-editor__editable {
            min-height: 400px; /* CKEditor içeriği için minimum yükseklik */
            max-height: 800px; /* CKEditor içeriği için maksimum yükseklik */
            overflow-y: auto;  /* Fazla içeriği kaydırılabilir hale getir */
        }
    </style>
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
                        <h3 class="card-title m-0">Yeni Makale Ekle</h3>
                    </div>
                    <div class="card-body">
                        <?php if($message): ?>
                            <?php echo $message; ?>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Başlık</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="content" class="form-label">İçerik</label>
                                <textarea class="form-control" id="content" name="content" rows="10" required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="author_id" class="form-label">Yazar</label>
                                <select class="form-select" id="author_id" name="author_id" required>
                                    <option value="">Yazar Seçin</option>
                                    <?php foreach ($authors as $author): ?>
                                        <option value="<?php echo $author['id']; ?>">
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
                                        <option value="<?php echo $category['id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="image" class="form-label">Resim</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                <small class="text-muted">Desteklenen formatlar: JPG, JPEG, PNG, GIF (Max: 5MB)</small>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Makale Ekle</button>
                                <a href="index.php" class="btn btn-secondary">İptal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Initialize CKEditor -->
    <script>
        CKEDITOR.replace('content',{
            height: 400
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
