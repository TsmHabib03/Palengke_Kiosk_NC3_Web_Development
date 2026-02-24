<?php
/**
 * Header Partial
 * 
 * Includes site navigation and user authentication status
 * Expects session to be started in config.php
 */
?>
<header class="site-header">
    <div class="container topbar-inner">
        <!-- Brand Logo -->
        <a class="brand" href="index.php" aria-label="Palengke Kiosk - Home">
            <span class="brand-mark" aria-hidden="true">PK</span>
            <span class="brand-text">Palengke Kiosk</span>
        </a>

        <!-- Main Navigation -->
        <nav class="nav" aria-label="Main navigation">
            <ul class="nav-list">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Logged In: Show role-based dashboard link -->
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li>
                            <a class="btn btn-primary" href="seller.php">
                                Seller Dashboard
                            </a>
                        </li>
                    <?php else: ?>
                        <li>
                            <a class="btn btn-primary" href="buyer.php">
                                Go to Market
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- User greeting -->
                    <li>
                        <span class="nav-user">
                            Hello, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>
                        </span>
                    </li>
                    
                    <!-- Logout -->
                    <li>
                        <a class="btn btn-ghost" href="logout.php">
                            Sign Out
                        </a>
                    </li>
                <?php else: ?>
                    <!-- Logged Out: Show auth links -->
                    <li>
                        <a class="btn btn-ghost" href="login.php">
                            Sign In
                        </a>
                    </li>
                    <li>
                        <a class="btn btn-primary" href="register.php">
                            Get Started
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
