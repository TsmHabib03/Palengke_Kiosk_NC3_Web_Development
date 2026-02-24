<?php
require_once 'config.php';

// Fetch some products for the display
$products_query = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC LIMIT 8");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Palengke Kiosk - A modern campus marketplace for fresh goods from local sellers">
    <title>Palengke Kiosk - Campus Market</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <?php include 'partials/header.php'; ?>

    <main class="site-shell landing">
        <!-- Hero Section -->
        <section class="hero" aria-labelledby="hero-heading">
            <span class="hero-tag">Campus Market Hub</span>
            <h1 id="hero-heading">Fresh goods from local sellers, all in one place.</h1>
            <p class="hero-copy">
                A modern marketplace connecting students with quality products from trusted campus vendors.
            </p>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <div class="hero-actions">
                    <a href="register.php" class="btn btn-primary btn-lg">Create Account</a>
                    <a href="login.php" class="btn btn-secondary btn-lg">Sign In</a>
                </div>
            <?php endif; ?>
        </section>

        <!-- Featured Products Section -->
        <section class="catalog" aria-labelledby="catalog-heading">
            <header class="section-head">
                <h2 id="catalog-heading">Featured Products</h2>
                <p class="text-muted">Latest items from our sellers</p>
            </header>

            <?php if (mysqli_num_rows($products_query) > 0): ?>
                <div class="product-grid">
                    <?php while ($row = mysqli_fetch_assoc($products_query)): ?>
                        <?php $img = !empty($row['image']) ? $row['image'] : null; ?>
                        <article class="product-card">
                            <figure class="product-media">
                                <?php if ($img): ?>
                                    <img src="<?= htmlspecialchars($img) ?>" 
                                         alt="<?= htmlspecialchars($row['name']) ?>"
                                         loading="lazy">
                                <?php else: ?>
                                    <div class="no-image">
                                        <span>No image available</span>
                                    </div>
                                <?php endif; ?>
                            </figure>
                            <div class="product-body">
                                <h3 class="product-title"><?= htmlspecialchars($row['name']) ?></h3>
                                <p class="product-price">&#8369;<?= number_format($row['price'], 2) ?></p>
                                <span class="badge badge-accent"><?= (int) $row['quantity'] ?> in stock</span>
                                <a href="login.php" class="btn btn-outline btn-sm">View Details</a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <h3>No products available</h3>
                    <p>Check back later for new items from our sellers.</p>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <?php include 'partials/footer.php'; ?>

</body>
</html>
