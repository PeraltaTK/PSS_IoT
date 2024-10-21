<nav>
    <ul class="nav justify-content-center">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link">
                <i class="fas fa-home"></i> Painel
            </a>
        </li>
        <li class="nav-item">
            <a href="fielddata.php" class="nav-link active">
                <i class="fas fa-leaf"></i> Dados do Campo
            </a>
        </li>
        <li class="nav-item">
            <a href="irrigation.php" class="nav-link">
                <i class="fas fa-water"></i> Irrigação
            </a>
        </li>
        <li class="nav-item">
            <a href="well.php" class="nav-link">
                <i class="fas fa-tint"></i> Capacidade do Poço
            </a>
        </li>
        <!-- Dropdown for username and logout -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Olá, <?php echo htmlspecialchars($_SESSION['username']); ?>
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="logout.php">Sair</a></li>
            </ul>
        </li>
    </ul>
</nav>
