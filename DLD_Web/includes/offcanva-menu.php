<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvaDeliveryMenu" aria-labelledby="offcanvaDeliveryMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvaDeliveryMenuLabel"><?= $APPNAME ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="<?= $BASE_URL ?>delivery/home.php">Início</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $BASE_URL ?>delivery/addlocation.php">Cadastrar Localização</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $BASE_URL ?>delivery/findclient.php">Buscar cliente</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $BASE_URL ?>logout.php">Logout</a>
            </li>
        </ul>
        <hr>
        <span class="d-flex justify-content-start fs-4">Abrir WhatsApp</span>
        <div class="input-group">
            <input class="form-control" type="tel" placeholder="Insira o número do whatsapp..." aria-label="WhatsApp">
            <button class="btn btn-success" type="submit"><i class="bi bi-whatsapp"></i></button>
        </div>
    </div>
    <div class="offcanvas-footer text-center mb-5">
        <div class="row d-flex flex-column">
            <div class="col">
                <span>Versão v.<?= $APPVERSION ?></span>
            </div>
            <div class="col">
                <span class="d-inline-flex">Desenvolvido por FilipeBezerra.Dev.BR</span>
            </div>
        </div>
    </div>
</div>