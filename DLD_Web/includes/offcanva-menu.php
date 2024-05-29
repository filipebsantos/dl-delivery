<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvaDeliveryMenu" aria-labelledby="offcanvaDeliveryMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvaDeliveryMenuLabel"><?= APP_NAME ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <span class="mb-5 fs-2 fw-bolder">Olá, <?= $_SESSION["activeUser"]["firstname"] ?>!</span>
        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
            <li class="nav-item mb-2">
                <div class="d-grid gap-2">
                    <a class="btn btn-primary" aria-current="page" href="<?= $BASE_URL ?>delivery/home.php">Início</a>
                </div>    
            </li>
            <li class="nav-item mb-2">
                <div class="d-grid gap-2">
                    <a class="btn btn-primary" href="<?= $BASE_URL ?>delivery/addlocation.php">Cadastrar Localização</a>
                </div>
            </li>
            <li class="nav-item mb-2">
                <div class="d-grid gap-2">    
                    <a class="btn btn-primary" href="<?= $BASE_URL ?>delivery/findclient.php">Buscar Cliente</a>
                </div>
            </li>
            <li class="nav-item mb-2">
                <div class="d-grid gap-2">
                    <a class="btn btn-primary" href="<?= $BASE_URL ?>logout.php">Logout</a>
                </div>
            </li>
        </ul>
        <hr>
        <span class="d-flex justify-content-start fs-4">Abrir WhatsApp</span>
        <div class="input-group">
            <input class="form-control" type="tel" placeholder="DDD + Número" aria-label="WhatsApp" name="wppTel" id="wppTel">
            <button class="btn btn-success" type="button" onclick="openWhatsApp()"><i class="bi bi-whatsapp"></i></button>
        </div>
    </div>
    <div class="offcanvas-footer text-center mb-5">
        <div class="row d-flex flex-column">
            <div class="col">
                <span>Versão v.<?= APP_VERSION ?></span>
            </div>
            <div class="col">
                <span class="d-inline-flex">Desenvolvido por FilipeBezerra.Dev.BR</span>
            </div>
        </div>
    </div>
</div>