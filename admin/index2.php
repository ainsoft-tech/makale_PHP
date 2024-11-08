<?php
// index.php
require_once 'config.php';

// Pagination variables
$limit = 9; // Number of articles per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Get the total number of articles
$stmt = $db->query("SELECT COUNT(*) as total FROM articles");
$total = $stmt->fetch()['total'];
$totalPages = ceil($total / $limit);

// Fetch categories
$categories = $db->query("SELECT id, name FROM categories")->fetchAll();

// Fetch authors
$authors = $db->query("SELECT id, name FROM authors")->fetchAll();

// Category filter
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;

// Author filter
$author_id = isset($_GET['author_id']) ? (int)$_GET['author_id'] : null;

// Filtering query
if ($category_id) {
    $stmt = $db->prepare("SELECT * FROM articles WHERE category_id = :category_id" . ($author_id ? " AND author_id = :author_id" : "") . " ORDER BY created_at DESC LIMIT :start, :limit");
    $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
    if ($author_id) {
        $stmt->bindValue(':author_id', $author_id, PDO::PARAM_INT);
    }
} else {
    $stmt = $db->prepare("SELECT * FROM articles" . ($author_id ? " WHERE author_id = :author_id" : "") . " ORDER BY created_at DESC LIMIT :start, :limit");
    if ($author_id) {
        $stmt->bindValue(':author_id', $author_id, PDO::PARAM_INT);
    }
}
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Makale Yönetim Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        footer {
            background-color: #f8f9fa;
            padding: 20px 0;
            text-align: center;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <!-- Header & Navbar -->
    <header class="bg-dark text-white py-3">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h4 mb-0">Makale Yönetimi</h1>
                    <p class="mb-0">Adres: SiteAdresiniz.com | Tel: 555-555-5555</p>
                </div>
                <nav>
                    <a class="btn btn-primary" href="index.php">Anasayfa</a>
                    <a class="btn btn-primary" href="add_article.php">Yeni Makale</a>
                    <a class="btn btn-primary" href="add_category.php">Kategori Ekle</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row">
            <!-- Center - Articles Section -->
            <main class="col-md-9">
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php foreach($articles as $article): ?>
                        <div class="col">
                            <div class="card h-100">
                                <?php if($article['image']): ?>
                                    <div class="card-img-container">
                                        <a href="view_article.php?id=<?php echo $article['id']; ?>">
                                            <img src="uploads/<?php echo htmlspecialchars($article['image']); ?>" 
                                                 class="card-img-top" 
                                                 alt="<?php echo htmlspecialchars($article['title']); ?>">
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="view_article.php?id=<?php echo $article['id']; ?>" 
                                           class="text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($article['title']); ?>
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted">
                                        <?php echo substr(strip_tags($article['content']), 0, 150) . '...'; ?>
                                    </p>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <small class="text-muted">
                                        <?php echo date('d.m.Y', strtotime($article['created_at'])); ?>
                                    </small>
                                </div>
                                <div class="btn-group">
                                    <a href="edit_article.php?id=<?php echo $article['id']; ?>" class="btn btn-warning btn-sm w-50">Düzenle</a>
                                    <a href="delete_article.php?id=<?php echo $article['id']; ?>" class="btn btn-danger btn-sm w-50" onclick="return confirm('Bu makaleyi silmek istediğinize emin misiniz?')">Sil</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <nav aria-label="Sayfa gezintisi">
                    <ul class="pagination justify-content-center mt-4">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="index.php?page=<?php echo $page - 1; ?>&category_id=<?php echo $category_id; ?>&author_id=<?php echo $author_id; ?>">Önceki</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="index.php?page=<?php echo $i; ?>&category_id=<?php echo $category_id; ?>&author_id=<?php echo $author_id; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="index.php?page=<?php echo $page + 1; ?>&category_id=<?php echo $category_id; ?>&author_id=<?php echo $author_id; ?>">Sonraki</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </main>

            <!-- Right Sidebar - Categories and Authors -->
            <aside class="col-md-3">
                <!-- Categories -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title m-0">Kategoriler</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="index.php">
                            <div class="mb-3">
                                <select name="category_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">Tüm Kategoriler</option>
                                    <?php foreach($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo $category_id == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Filtrele</button>
                        </form>
                    </div>
                </div>
                
                <!-- Authors -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title m-0">Yazarlar</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="index.php">
                            <div class="mb-3">
                                <select name="author_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">Tüm Yazarlar</option>
                                    <?php foreach($authors as $author): ?>
                                        <option value="<?php echo $author['id']; ?>" <?php echo $author_id == $author['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($author['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Filtrele</button>
                        </form>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p class="mb-1">© <?php echo date("Y"); ?> Makale Yönetim Sistemi. Tüm hakları saklıdır.</p>
            <p>Adres: SiteAdresiniz.com | Tel: 555-555-5555 | E-posta: info@siteadresiniz.com</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
