<?php
require_once("includes/header.php");
require_once("includes/db.php");
require_once("dao/LocationDAO.php");

$returnMessage = $message->getMessage();

if (!empty($returnMessage)) {
    $message->clearMessage();
}

if (isset($_GET["locid"])) {
    $locId = filter_input(INPUT_GET, "locid", FILTER_SANITIZE_NUMBER_INT);

    if ($locId) {

        $locationDAO = new LocationDAO($dbConn);
        $location = $locationDAO->getLocation(intval($locId));

        if (!$location) {

            header("Location: ". $BASE_URL . "location.php");
        }
    } else {

        header("Location: ". $BASE_URL . "location.php");
    }
}
?>
<div class="container-fluid">
    <div class="row">
        <!-- Include sidebar -->
        <?php include("includes/sidebar.php"); ?>
        <!-- Include content -->
        <div id="page-content" class="col-auto col-md-7">
            <div class="container mt-5">
                <?php if (!empty($returnMessage["msg"])) : ?>
                    <div class="container mt-3" id="alert-box">
                        <div class="alert alert-<?= $returnMessage["type"] ?>" role="alert">
                            <?= $returnMessage["msg"] ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="container">
                    <div class="row">
                        <div class="col-10">
                            <h3 class="mb-4">Editar localização</h3>
                        </div>
                        <div class="col align-self-center">
                            <a href="<?= $BASE_URL . "location.php?clie=" . $location["clientid"] ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-caret-left-fill"></i>
                                Voltar
                            </a>
                        </div>
                    </div>

                    <form action="<?= $BASE_URL ?>locationprocess.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="txtLocationId" value="<?= $location["id"] ?>">

                        <div class="row mb-3">
                            <div class="col-sm-12 col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text" id="txtClientId">ID</span>
                                    <input type="text" name="txtClientId" class="form-control" value="<?= $location["clientid"] ?>" disabled>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-9">
                                <div class="input-group">
                                    <span class="input-group-text" id="txtClientName">Cliente</span>
                                    <input type="text" class="form-control" value="<?= $location["name"] ?>" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-auto col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text" id="txtCoordinates">Coordenadas</span>
                                    <input type="text" name="txtCoordinates" class="form-control" value="<?= $location["latitude"] . ", " . $location["longitude"] ?>" required>
                                </div>
                            </div>

                            <div class="col-auto col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text" id="optNeighborhood">Bairro</span>
                                    <select class="form-select" name="optNeighborhood" id="optNeighborhood" autocomplete="on" required>
                                        <option value="ALAGADIÇO" <?= $location["neighborhood"] == "ALAGADIÇO" ? "selected" : "" ?>>Alagadiço</option>
                                        <option value="ATLÂNTICO" <?= $location["neighborhood"] == "ATLÂNTICO" ? "selected" : "" ?>>Atlântico</option>
                                        <option value="BARRA" <?= $location["neighborhood"] == "BARRA" ? "selected" : "" ?>>Barra</option>
                                        <option value="BOCA DO POÇO" <?= $location["neighborhood"] == "BOCA DO POÇO" ? "selected" : "" ?>>Boca do Poço</option>
                                        <option value="BOI MORTO" <?= $location["neighborhood"] == "BOI MORTO" ? "selected" : "" ?>>Boi Morto</option>
                                        <option value="CABRA MORTA" <?= $location["neighborhood"] == "CABRA MORTA" ? "selected" : "" ?>>Cabra Morta</option>
                                        <option value="CAMPO DE AVIAÇÃO" <?= $location["neighborhood"] == "CAMPO DE AVIAÇÃO" ? "selected" : "" ?>>Campo de Aviação</option>
                                        <option value="CAMPO DE SEMENTE" <?= $location["neighborhood"] == "CAMPO DE SEMENTE" ? "selected" : "" ?>>Campo de Semente</option>
                                        <option value="CARLOTAS" <?= $location["neighborhood"] == "CARLOTAS" ? "selected" : "" ?>>Carlotas</option>
                                        <option value="CARRASCO" <?= $location["neighborhood"] == "CARRASCO" ? "selected" : "" ?>>Carrasco</option>
                                        <option value="CCF" <?= $location["neighborhood"] == "CCF" ? "selected" : "" ?>>CCF</option>
                                        <option value="CENTRO" <?= $location["neighborhood"] == "CENTRO" ? "selected" : "" ?>>Centro</option>
                                        <option value="CONJUNTO NOVA ESPERANÇA" <?= $location["neighborhood"] == "CONJUNTO NOVA ESPERANÇA" ? "selected" : "" ?>>Conj. Nova Esperança</option>
                                        <option value="COREIA" <?= $location["neighborhood"] == "COREIA" ? "selected" : "" ?>>Coreia</option>
                                        <option value="FREXEIRA" <?= $location["neighborhood"] == "FREXEIRA" ? "selected" : "" ?>>Frexeira</option>
                                        <option value="JARDIM DE BAIXO" <?= $location["neighborhood"] == "JARDIM DE BAIXO" ? "selected" : "" ?>>Jardim de Baixo</option>
                                        <option value="JARDIM DE CIMA" <?= $location["neighborhood"] == "JARDIM DE CIMA" ? "selected" : "" ?>>Jardim de Cima</option>
                                        <option value="JARDIM DO MEIO" <?= $location["neighborhood"] == "JARDIM DO MEIO" ? "selected" : "" ?>>Jardim do Meio</option>
                                        <option value="LAGOA GRANDE" <?= $location["neighborhood"] == "LAGOA GRANDE" ? "selected" : "" ?>>Lagoa Grande</option>
                                        <option value="LAGOA" <?= $location["neighborhood"] == "LAGOA" ? "selected" : "" ?>>Lagoa</option>
                                        <option value="MALEITAS" <?= $location["neighborhood"] == "MALEITAS" ? "selected" : "" ?>>Maleitas</option>
                                        <option value="MOCÓ" <?= $location["neighborhood"] == "MOCÓ" ? "selected" : "" ?>>Mocó</option>
                                        <option value="PARACURU BEACH" <?= $location["neighborhood"] == "PARACURU BEACH" ? "selected" : "" ?>>Paracuru Beach</option>
                                        <option value="PARAZINHO" <?= $location["neighborhood"] == "PARAZINHO" ? "selected" : "" ?>>Parazinho</option>
                                        <option value="PLANALTO DA BARRA" <?= $location["neighborhood"] == "PLANALTO DA BARRA" ? "selected" : "" ?>>Planalto da Barra</option>
                                        <option value="POÇO DOCE" <?= $location["neighborhood"] == "POÇO DOCE" ? "selected" : "" ?>>Poço Doce</option>
                                        <option value="RIACHO DOCE" <?= $location["neighborhood"] == "RIACHO DOCE" ? "selected" : "" ?>>Riacho Doce</option>
                                        <option value="SÃO PEDRO DE BAIXO" <?= $location["neighborhood"] == "SÃO PEDRO DE BAIXO" ? "selected" : "" ?>>São Pedro de Baixo</option>
                                        <option value="SÃO PEDRO" <?= $location["neighborhood"] == "SÃO PEDRO" ? "selected" : "" ?>>São Pedro</option>
                                        <option value="SÍTIO INGLÊS" <?= $location["neighborhood"] == "SÍTIO INGLÊS" ? "selected" : "" ?>>Sítio Inglês</option>
                                        <option value="TORRE" <?= $location["neighborhood"] == "TORRE" ? "selected" : "" ?>>Torre</option>
                                        <option value="VILA SÃO JOSÉ" <?= $location["neighborhood"] == "VILA SÃO JOSÉ" ? "selected" : "" ?>>Vila São José</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <div class="input-group">
                                <span class="input-group-text" id="optLocationType">Tipo de Localização</span>
                                    <select class="form-select" name="optLocationType" id="optLocationType" autocomplete="on" required>
                                        <option value="RESIDENCIA" <?= $location["type"] == "RESIDENCIA" ? "selected" : "" ?>>Residência</option>
                                        <option value="TRABALHO" <?= $location["type"] == "TRABALHO" ? "selected" : "" ?>>Trabalho</option>
                                        <option value="FAMILIAR" <?= $location["type"] == "FAMILIAR" ? "selected" : "" ?>>Familiar</option>
                                        <option value="ENTREGA" <?= $location["type"] == "ENTREGA" ? "selected" : "" ?>>Entrega</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col">
                                <div class="input-group">
                                    <label class="input-group-text" for="imgHousePic">Foto da casa</label>
                                    <input type="file" class="form-control" name="imgHousePic" id="imgHousePic" accept="image/png, image/jpeg">
                                </div>
                            </div>
                        </div>

                        <div class="input-group mb-3">
                            <label class="input-group-text" for="txtObs">Obervações</label>
                            <textarea class="form-control" name="txtObs" id="txtObs" maxlength="255" rows="3"><?= $location["obs"] ?></textarea>
                        </div>

                        <div class="row mt-3">
                            <div class="col d-flex align-items-center justify-content-center">
                                <input type="submit" class="btn btn-primary mt-2" value="Editar Localização">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include("includes/footer.php"); ?>