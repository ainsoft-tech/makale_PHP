<?php
// index.php
require_once 'config.php';

// Number of articles per page
$articlesPerPage = 10;

// Determine the current page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Ensure page is at least 1

// Calculate the offset for the SQL query
$offset = ($page - 1) * $articlesPerPage;

// Get the total number of articles
$totalArticlesStmt = $db->query("SELECT COUNT(*) FROM articles");
$totalArticles = $totalArticlesStmt->fetchColumn();
$totalPages = ceil($totalArticles / $articlesPerPage);

// Fetch articles for the current page
$stmt = $db->prepare("SELECT articles.*, authors.name AS author_name, categories.name AS category_name 
                      FROM articles 
                      LEFT JOIN authors ON articles.author_id = authors.id 
                      LEFT JOIN categories ON articles.category_id = categories.id 
                      ORDER BY articles.created_at DESC 
                      LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $articlesPerPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Makale Yönetimi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Make rows clickable */
        .clickable-row {
            cursor: pointer;
        }

        footer {
            background-color: #f8f9fa;
            padding: 20px 0;
            text-align: center;
            margin-top: 40px;
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
        <div class="row">
            <div class="col col-md-9">
                <h1 class="mb-3">Makaleler</h1>
            </div>

            <div class="col col-md-3 mt-4 text-end">
                <p>
                    <span>
                        <a class="btn btn btn-outline-primary" href="add_article.php">Makale Ekle</a>
                        <a class="btn btn btn-outline-primary" href="add_category.php">Kategori Ekle</a>
                    </span>
                </p>
            </div>
            <div class="container mt-2">
                <div class="p-5 mb-3 bg-light rounded-5">
                    <div class="container-fluid py-5">
                        <h1 class="display-5 fw-bold">Hoş Geldiniz</h1>
                        <p class="col-md-9 fs-6">Bu sayfa üzerinde en yeni makaleleri ve güncellemeleri bulabilirsiniz. Hedefimiz, sizlere kaliteli içerik sunmaktır.</p>
                        <a href="#" class="btn btn-primary btn-lg">Daha Fazla Bilgi Al</a>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Başlık</th>
                        <th>Yazar</th>
                        <th>Kategori</th>
                        <th>Tarih</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($articles as $article): ?>
                        <tr class="clickable-row" onclick="window.location.href='view_article.php?id=<?php echo $article['id']; ?>'">
                            <td><?php echo htmlspecialchars($article['title']); ?></td>
                            <td><?php echo htmlspecialchars($article['author_name']); ?></td>
                            <td><?php echo htmlspecialchars($article['category_name']); ?></td>
                            <td><?php echo date('d-m-Y', strtotime($article['created_at'])); ?></td>
                            <td class="text-center" style="width:1%; white-space: nowrap;">
                                <a href="edit_article.php?id=<?php echo $article['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i> Düzenle
                                </a>
                                <a href="delete_article.php?id=<?php echo $article['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bu makaleyi silmek istediğinize emin misiniz?')">
                                    <i class="bi bi-trash"></i> Sil
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <!-- Previous page link -->
                <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>" tabindex="-1">Önceki</a>
                </li>

                <!-- Page number links -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <!-- Next page link -->
                <li class="page-item <?php if ($page >= $totalPages) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">Sonraki</a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p class="mb-1 text-align-center">© <?php echo date("Y"); ?> Makale Yönetim Sistemi. Tüm hakları saklıdır.</p>
            <p>Adres: SiteAdresiniz.com | Tel: 555-555-5555 | E-posta: info@siteadresiniz.com</p>
        </div>
    </footer>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
