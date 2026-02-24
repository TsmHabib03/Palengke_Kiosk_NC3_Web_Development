<?php
require_once 'config.php';

/* CHECK IF LOGGED IN AS BUYER */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php?error=unauthorized");
    exit();
}

/* BUY PRODUCT */
if (isset($_POST['buy'])) {
    $id = intval($_POST['id']);
    $qty = intval($_POST['buy_qty']);

    $product_query = mysqli_query($conn, "SELECT * FROM products WHERE id=$id");
    $product = mysqli_fetch_assoc($product_query);

    if ($product && $qty <= $product['quantity'] && $qty > 0) {
        $total = $qty * $product['price'];

        // Insert sale
        $insert = mysqli_query($conn, "INSERT INTO sales (product_name, quantity, total)
                             VALUES ('{$product['name']}', '$qty', '$total')");

        // Update stock
        if ($insert) {
            mysqli_query($conn, "UPDATE products
                                 SET quantity = quantity - $qty
                                 WHERE id=$id");
            $success_msg = "Successfully bought $qty {$product['name']}.";
        }
    } else {
        $error_msg = "Invalid quantity or out of stock.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Browse and buy products from campus sellers">
    <title>Marketplace - Palengke Kiosk</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <?php include 'partials/header.php'; ?>

    <main class="site-shell dashboard">
        <!-- Page Header -->
        <header class="page-head">
            <h1>Fresh Arrivals</h1>
            <p class="text-muted">Explore products from local sellers</p>
        </header>

        <!-- Alerts -->
        <?php if (isset($success_msg)): ?>
            <div class="alert alert-success" role="alert">
                <?= htmlspecialchars($success_msg) ?>
            </div>
        <?php endif; ?>
        <?php if (isset($error_msg)): ?>
            <div class="alert alert-error" role="alert">
                <?= htmlspecialchars($error_msg) ?>
            </div>
        <?php endif; ?>

        <!-- Product Grid -->
        <section class="market-grid" aria-label="Available products">
            <?php
            $result = mysqli_query($conn, "SELECT * FROM products WHERE quantity > 0");
            if (mysqli_num_rows($result) > 0):
                while ($row = mysqli_fetch_assoc($result)):
                    $img_src = !empty($row['image']) ? $row['image'] : null;
            ?>
                <article class="card">
                    <figure class="card-img">
                        <?php if ($img_src): ?>
                            <img src="<?= htmlspecialchars($img_src) ?>" 
                                 alt="<?= htmlspecialchars($row['name']) ?>"
                                 loading="lazy">
                        <?php else: ?>
                            <div class="placeholder">
                                <span>No Image</span>
                            </div>
                        <?php endif; ?>
                    </figure>

                    <div class="card-content">
                        <h2 class="card-title"><?= htmlspecialchars($row['name']) ?></h2>
                        <div class="card-meta">
                            <span class="price text-accent">&#8369;<?= number_format($row['price'], 2) ?></span>
                            <span class="badge"><?= (int) $row['quantity'] ?> in stock</span>
                        </div>

                        <form method="POST" class="card-actions" aria-label="Purchase form for <?= htmlspecialchars($row['name']) ?>">
                            <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
                            <input 
                                class="input qty-input" 
                                type="number" 
                                name="buy_qty" 
                                value="1" 
                                min="1" 
                                max="<?= (int) $row['quantity'] ?>" 
                                required
                                aria-label="Quantity">
                            <button type="submit" name="buy" class="btn btn-primary btn-sm">
                                Buy Now
                            </button>
                        </form>
                    </div>
                </article>
            <?php
                endwhile;
            else:
            ?>
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
