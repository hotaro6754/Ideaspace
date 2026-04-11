<?php
/**
 * navbar.php - Main Navigation Component
 * Reusable across all pages
 */

$current_user = getCurrentUser();
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        .navbar {
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-menu {
            display: flex;
            gap: 2rem;
            align-items: center;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .navbar-menu a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.2s;
        }

        .navbar-menu a:hover {
            opacity: 0.8;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #06B6D4;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            cursor: pointer;
        }

        .dropdown-menu {
            position: absolute;
            top: 60px;
            right: 20px;
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
            min-width: 200px;
            display: none;
            z-index: 1000;
        }

        .dropdown-menu.active {
            display: block;
        }

        .dropdown-menu a {
            display: block;
            padding: 0.75rem 1rem;
            color: #1E293B;
            text-decoration: none;
            border-bottom: 1px solid #F1F5F9;
        }

        .dropdown-menu a:last-child {
            border-bottom: none;
        }

        .dropdown-menu a:hover {
            background: #F1F5F9;
        }

        .navbar-mobile-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .navbar-mobile-toggle {
                display: block;
            }

            .navbar-menu {
                position: absolute;
                top: 60px;
                left: 0;
                right: 0;
                background: #0F172A;
                flex-direction: column;
                gap: 0;
                display: none;
                width: 100%;
            }

            .navbar-menu.active {
                display: flex;
            }

            .navbar-menu a {
                padding: 1rem 2rem;
                border-bottom: 1px solid rgba(255,255,255,0.1);
            }
        }
    </style>
</head>
<body>
<nav class="navbar">
    <a href="<?php echo BASE_URL; ?>" class="navbar-brand">
        <i class="fas fa-lightbulb"></i>
        IdeaSync
    </a>

    <button class="navbar-mobile-toggle" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
    </button>

    <ul class="navbar-menu" id="navbarMenu">
        <?php if ($current_user): ?>
            <li><a href="<?php echo BASE_URL; ?>/ideas">Ideas</a></li>
            <li><a href="<?php echo BASE_URL; ?>/agents/dashboard">Dashboard</a></li>
            <li><a href="<?php echo BASE_URL; ?>/workflow">Workflow</a></li>
            <li><a href="<?php echo BASE_URL; ?>/leaderboard">Leaderboard</a></li>
            <li><a href="<?php echo BASE_URL; ?>/messages">Messages</a></li>
        <?php else: ?>
            <li><a href="<?php echo BASE_URL; ?>/ideas">Browse Ideas</a></li>
        <?php endif; ?>
    </ul>

    <div class="navbar-user">
        <?php if ($current_user): ?>
            <div class="user-avatar" onclick="toggleDropdown()" title="<?php echo $current_user['name']; ?>">
                <?php echo strtoupper(substr($current_user['name'], 0, 1)); ?>
            </div>
            <div class="dropdown-menu" id="dropdownMenu">
                <a href="<?php echo BASE_URL; ?>/profile">My Profile</a>
                <a href="<?php echo BASE_URL; ?>/preferences">Preferences</a>
                <a href="<?php echo BASE_URL; ?>/auth/logout">Logout</a>
            </div>
        <?php else: ?>
            <a href="<?php echo BASE_URL; ?>/auth/login" style="color: white; text-decoration: none;">Login</a>
        <?php endif; ?>
    </div>
</nav>

<script>
    function toggleMenu() {
        document.getElementById('navbarMenu').classList.toggle('active');
    }

    function toggleDropdown() {
        document.getElementById('dropdownMenu').classList.toggle('active');
    }

    document.addEventListener('click', function(event) {
        if (!event.target.closest('.navbar-user')) {
            document.getElementById('dropdownMenu').classList.remove('active');
        }
    });
</script>
</body>
</html>
