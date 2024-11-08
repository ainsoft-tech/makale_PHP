<?php
// view_article.php
require_once 'config.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

try {
    // Görüntülenme sayısını artır
    $stmt = $db->prepare("UPDATE articles SET view_count = view_count + 1 WHERE id = ?");
    $stmt->execute([$id]);
    
    // Makale bilgilerini, yazar adını ve kategori adını getir
    $stmt = $db->prepare("
        SELECT a.*, c.name as category_name, au.name as author_name 
        FROM articles a 
        LEFT JOIN categories c ON a.category_id = c.id 
        LEFT JOIN authors au ON a.author_id = au.id 
        WHERE a.id = ?
    ");
    $stmt->execute([$id]);
    $article = $stmt->fetch();
    
    if (!$article) {
        header('Location: index.php');
        exit;
    }
} catch(PDOException $e) {
    die("Bir hata oluştu: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="//cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <style>
        .article-image {
            max-height: 600px;
            object-fit: cover;
            width: 100%;
        }
        .article-meta {
            color: #6c757d;
            font-size: 0.9rem;
        }
        /* CKEditor için özel bir sınıf ekleyerek yüksekliği kontrol edelim */
        .ck-editor__editable {
            min-height: 600px; /* CKEditor içeriği için minimum yükseklik */
            max-height: 800px; /* CKEditor içeriği için maksimum yükseklik */
            overflow-y: auto;  /* Fazla içeriği kaydırılabilir hale getir */
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Makale Yönetimi</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">Ana Sayfa</a></li>
                        <li class="breadcrumb-item active"><?php echo htmlspecialchars($article['title']); ?></li>
                    </ol>
                </nav>

                <article class="card">
                    <?php if($article['image']): ?>
                        <img src="uploads/<?php echo htmlspecialchars($article['image']); ?>" 
                             class="card-img-top article-image" 
                             alt="<?php echo htmlspecialchars($article['title']); ?>">
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <h1 class="card-title mb-4"><?php echo htmlspecialchars($article['title']); ?></h1>
                        
                        <div class="article-meta mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <i class="bi bi-person"></i> Yazar: <?php echo htmlspecialchars($article['author_name']); ?>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <i class="bi bi-calendar"></i> 
                                    Tarih: <?php echo date('d.m.Y H:i', strtotime($article['created_at'])); ?>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <i class="bi bi-tags"></i> Kategori: <?php echo htmlspecialchars($article['category_name']); ?>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <i class="bi bi-eye"></i> 
                                    Görüntülenme: <?php echo number_format($article['view_count']); ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- İçeriği CKEditor ile sadece görüntülenebilir şekilde göster -->
                        <div id="articleContent" class="article-content">
                            <?php echo $article['content']; ?>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Geri
                            </a>
                            <div>
                                <a href="edit_article.php?id=<?php echo $article['id']; ?>" class="btn btn-primary">
                                    <i class="bi bi-pencil"></i> Düzenle
                                </a>
                                <a href="delete_article.php?id=<?php echo $article['id']; ?>" 
                                   class="btn btn-danger" 
                                   onclick="return confirm('Bu makaleyi silmek istediğinizden emin misiniz?')">
                                    <i class="bi bi-trash"></i> Sil
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // CKEditor'u sadece görüntüleme amacıyla yükleyin ve yüksekliği ayarlayın
        CKEDITOR.replace('articleContent', {
            readOnly: true,
            toolbar: [],
            height: 600 // CKEditor içeriği için yüksekliği ayarlayın
        });
    </script>
</body>
</html>
