
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Makale Yönetim Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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
    <!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header with Social Media Icons</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <img src="img/logo.png" alt="Site Logo" width="75" height="75" class="me-2">
        <a class="navbar-brand" href="index.php">Site Adı</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="index.php">Anasayfa</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="hakkinda.php">Hakkında</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">İletişim</a>
                </li>
            </ul>
            
            <!-- Social Media Icons -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="https://facebook.com" target="_blank">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://twitter.com" target="_blank">
                        <i class="fab fa-twitter"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://instagram.com" target="_blank">
                        <i class="fab fa-instagram"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://linkedin.com" target="_blank">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

    
    <div class="container mt-3">
        <div class="p-5 mb-3 bg-light rounded-5">
            <div class="container-fluid py-5">
                <h1 class="display-5 fw-bold">Hoş Geldiniz</h1>
                <p class="col-md-8 fs-6">Bu sayfa üzerinde en yeni makaleleri ve güncellemeleri bulabilirsiniz. Hedefimiz, sizlere kaliteli içerik sunmaktır.</p>
                <a href="#" class="btn btn-primary btn-lg">Daha Fazla Bilgi Al</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="row">
            <!-- Center - Articles Section -->
            <main class="col-md-9">
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">

                    <p>HAKKINDA İÇERİĞİ BURAYA GELECEK...</p>

                </div>



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


    <!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
