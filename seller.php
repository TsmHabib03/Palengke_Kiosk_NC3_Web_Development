<?php
require_once 'config.php';

/* CHECK IF LOGGED IN AS ADMIN */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=unauthorized");
    exit();
}

/* ADD PRODUCT */
if (isset($_POST['add'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = $_POST['price'];
    $qty = $_POST['quantity'];

    // Default image
    $db_image_path = 'uploads/default.png';

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = time() . '_' . uniqid() . '.' . $file_ext;
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $db_image_path = $target_file;
        }
    }

    mysqli_query($conn, "INSERT INTO products (name, price, quantity, image)
                         VALUES ('$name', '$price', '$qty', '$db_image_path')");
}

/* DELETE PRODUCT */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Seller Dashboard - Manage your products and inventory">
    <title>Seller Dashboard - Palengke Kiosk</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <?php include 'partials/header.php'; ?>

    <main class="site-shell seller-page">
        <div class="seller-layout">
            <!-- Add Product Form -->
            <aside class="form-panel" aria-labelledby="add-product-heading">
                <h2 id="add-product-heading">Add New Product</h2>
                <p class="text-muted">Upload item details for your kiosk listing</p>

                <form method="POST" enctype="multipart/form-data" class="product-form" aria-label="Add new product form">
                    <div class="field">
                        <label class="label" for="image">Product Image</label>
                        <input 
                            class="input file-input" 
                            type="file" 
                            id="image" 
                            name="image" 
                            accept="image/*" 
                            required
                            aria-describedby="image-help">
                        <small id="image-help" class="text-muted">Upload a clear image of your product</small>
                    </div>

                    <div class="field">
                        <label class="label" for="name">Product Name</label>
                        <input 
                            class="input" 
                            type="text" 
                            id="name" 
                            name="name" 
                            placeholder="e.g., Fresh Mangoes"
                            required
                            maxlength="100">
                    </div>

                    <div class="form-row">
                        <div class="field">
                            <label class="label" for="price">Price (&#8369;)</label>
                            <input 
                                class="input" 
                                type="number" 
                                id="price" 
                                name="price" 
                                placeholder="0.00" 
                                step="0.01" 
                                min="0"
                                required>
                        </div>

                        <div class="field">
                            <label class="label" for="quantity">Stock Quantity</label>
                            <input 
                                class="input" 
                                type="number" 
                                id="quantity" 
                                name="quantity" 
                                placeholder="0"
                                min="0"
                                required>
                        </div>
                    </div>

                    <button type="submit" name="add" class="btn btn-primary btn-lg form-submit">
                        Publish Item
                    </button>
                </form>
            </aside>

            <!-- Inventory Section -->
            <section class="inventory-panel" aria-labelledby="inventory-heading">
                <header class="inventory-head">
                    <h2 id="inventory-heading">My Inventory</h2>
                    <p class="text-muted">Manage your posted products</p>
                </header>

                <div class="inventory-grid">
                    <?php
                    $result = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
                    if (mysqli_num_rows($result) > 0):
                        while ($row = mysqli_fetch_assoc($result)):
                            $img_src = !empty($row['image']) ? $row['image'] : 'uploads/default.png';
                    ?>
                        <article class="inventory-card">
                            <figure class="inventory-media">
                                <img src="<?= htmlspecialchars($img_src) ?>" 
                                     alt="<?= htmlspecialchars($row['name']) ?>"
                                     loading="lazy">
                            </figure>
                            <div class="inventory-body">
                                <h3><?= htmlspecialchars($row['name']) ?></h3>
                                <p class="inventory-price text-accent">&#8369;<?= number_format($row['price'], 2) ?></p>
                                <span class="badge badge-success"><?= (int) $row['quantity'] ?> in stock</span>
                            </div>
                            <div class="inventory-actions">
                                <a href="seller.php?delete=<?= (int) $row['id'] ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this item?')"
                                   aria-label="Delete <?= htmlspecialchars($row['name']) ?>">
                                    Delete
                                </a>
                            </div>
                        </article>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <div class="empty-state">
                            <h3>No products yet</h3>
                            <p>Add your first product using the form on the left</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <?php include 'partials/footer.php'; ?>

</body>
</html>
