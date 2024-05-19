<div id="side-menu" class="d-flex flex-column col-auto bg-dark min-vh-100">
    <div class="mt-4">
        <a href="#" class="text-white d-none d-sm-inline text-decoration-none d-flex align-items-center ms-5">
            <span class="fs-5">Menu</span>
        </a>
        <hr class="text-white" />
        <ul class="nav nav-pills flex-column mt-3 mt-sm-0" id="mainmenu">
            <li class="nav-item">
                <a href="<?= $BASE_URL ?>dashboard.php" class="nav-link text-white">
                    <i class="bi bi-calendar3"></i>
                    <span class="ms-2 d-none d-sm-inline">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= $BASE_URL ?>route.php" class="nav-link text-white">
                    <i class="bi bi-truck"></i>
                    <span class="ms-2 d-none d-sm-inline">Rotas</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= $BASE_URL ?>client.php" class="nav-link text-white">
                    <i class="bi bi-person-vcard"></i>
                    <span class="ms-2 d-none d-sm-inline">Clientes</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= $BASE_URL ?>location.php" class="nav-link text-white">
                    <i class="bi bi-pin-map"></i>
                    <span class="ms-2 d-none d-sm-inline">Coordenadas</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= $BASE_URL ?>user.php" class="nav-link text-white">
                    <i class="bi bi-people"></i>
                    <span class="ms-2 d-none d-sm-inline">Usuários</span>
                </a>
            </li>
            <hr class="text-white">
            <li class="nav-item disable">
                <a href="#usermenu" data-bs-toggle="collapse" class="nav-link text-white">
                    <i class="bi bi-person-circle"></i>
                    <span class="ms-2 d-none d-sm-inline"><?= isset($_SESSION["activeUser"]) ? $_SESSION["activeUser"]["firstname"] : "" ?></span>
                    <i class="bi bi-caret-down-fill"></i>
                </a>
                <ul class="nav collapse ms-1 flex-column" id="usermenu" data-bs-parent="#mainmenu">
                    <li class="nav-item">
                        <a href="#" class="nav-link text-white">Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= $BASE_URL ?>logout.php" class="nav-link text-white">Logout</a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>